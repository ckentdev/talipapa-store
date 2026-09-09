<?php

namespace App\Services\VoiceAssistant;

use App\Services\Voc\QueryAttributeExtractor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use OpenAI;

class VoiceNlpService
{
    public function __construct(
        private readonly QueryAttributeExtractor $queryAttributeExtractor,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function extract(string $text, string $context = 'default'): array
    {
        $normalized = $this->normalizeForCache($text);
        $cacheKey = 'voice_nlp_v2:'.hash('sha256', $normalized.'|'.$context);

        return Cache::remember($cacheKey, config('voice-assistant.cache_ttl', 3600), function () use ($text, $context) {
            return $this->extractFromApi($text, $context);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function extractFromApi(string $text, string $context): array
    {
        $apiKey = config('voice-assistant.openai_api_key');

        if (empty($apiKey)) {
            return $this->fallbackExtract($text);
        }

        try {
            $client = OpenAI::client($apiKey);
            $synonymLines = collect(config('voice-assistant.synonyms', []))
                ->map(fn (array $targets, string $source) => $source.' → '.implode(', ', $targets))
                ->take(20)
                ->implode("\n");

            $system = <<<PROMPT
You parse Filipino marketplace voice queries (English, Tagalog, Bisaya/Cebuano, or mixed).
Return ONLY valid JSON with keys:
- original_text (string, echo user input)
- language (string: English, Tagalog, Bisaya, or Mixed)
- keywords (string array, English product search terms + synonyms; omit stopwords and price words like barato/cheap)
- units (string array, normalized: 1L, 1kg, 500ml, 300ml, pcs)
- intent (one of: product_search, add_to_cart, set_payment_method, set_delivery_note, select_address, go_to_checkout_step, general_chat)
- quantity (integer, default 1)
- product (string or null, canonical: milk, tuyo, rice, ...)
- brand (string or null, e.g. nestle)
- dietary (string array: vegan, plant-based, non-cow, non-dairy)
- price_intent (cheap, premium, or null). barato/mura/cheap → cheap
- exclusions (string array). "dili sa baka" / "not from cow" / "hindi gatas ng baka" → ["cow"]
- checkout (object or null): payment_method (cod|ewallet|card), notes (string), address_label (string), address_index (int), step (int 1-5), product_id (int)

Examples:
- "barato na tuyo" → product tuyo, price_intent cheap
- "gatas na dili sa baka" → product milk, exclusions ["cow"], dietary ["non-cow"]
- "gatas na vegan 300 ml nestle" → product milk, brand nestle, dietary ["vegan","plant-based"], units ["300ml"], exclusions ["cow"]

Synonym hints:
{$synonymLines}

Unit rules: litro/liter→L, kilo→kg, milliliters→ml, pieces→pcs. "1 liter coke" → units ["1L","1000ml"].

COD phrases: cash on delivery, COD, bayad pag abot, cash lang.
Context: {$context}
PROMPT;

            $response = $client->chat()->create([
                'model' => config('voice-assistant.nlp_model', 'gpt-4o-mini'),
                'max_tokens' => config('voice-assistant.nlp_max_tokens', 500),
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $text],
                ],
            ]);

            $content = $response->choices[0]->message->content ?? '{}';
            $parsed = json_decode($content, true);

            if (! is_array($parsed)) {
                return $this->fallbackExtract($text);
            }

            return $this->normalizeParsed($parsed, $text);
        } catch (\Throwable) {
            return $this->fallbackExtract($text);
        }
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @return array<string, mixed>
     */
    private function normalizeParsed(array $parsed, string $text): array
    {
        $nlp = $this->queryAttributeExtractor->mergeParsed($parsed, $text);

        $intent = (string) ($nlp['intent'] ?? 'product_search');
        $allowed = [
            'product_search', 'add_to_cart', 'set_payment_method', 'set_delivery_note',
            'select_address', 'go_to_checkout_step', 'general_chat',
        ];

        if (! in_array($intent, $allowed, true)) {
            $nlp['intent'] = 'product_search';
        }

        return $nlp;
    }

    /**
     * @return array<string, mixed>
     */
    private function fallbackExtract(string $text): array
    {
        $nlp = $this->queryAttributeExtractor->extract($text);
        $lower = Str::lower($text);

        if (preg_match('/\b(cod|cash on delivery|bayad pag|cash lang)\b/i', $text)) {
            $nlp['intent'] = 'set_payment_method';
            $nlp['checkout'] = ['payment_method' => 'cod'];
        } elseif (preg_match('/\b(ibutang|delivery|note|gate|instruksyon)\b/i', $lower)
            && ! $nlp['product']) {
            $nlp['intent'] = 'set_delivery_note';
            $nlp['checkout'] = ['notes' => $text];
        }

        return $nlp;
    }

    private function normalizeForCache(string $text): string
    {
        return Str::lower(trim(preg_replace('/\s+/', ' ', $text) ?? $text));
    }
}
