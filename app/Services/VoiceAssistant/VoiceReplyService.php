<?php

namespace App\Services\VoiceAssistant;

use Illuminate\Support\Collection;
use OpenAI;
use RuntimeException;

class VoiceReplyService
{
    /**
     * @param  array<string, mixed>  $nlp
     * @param  Collection<int, \App\Models\Product>  $products
     * @param  array<string, mixed>  $actionResult
     */
    public function generate(array $nlp, Collection $products, array $actionResult = []): string
    {
        $apiKey = config('voice-assistant.openai_api_key');

        if (empty($apiKey)) {
            return $this->fallbackReply($nlp, $products, $actionResult);
        }

        $productLines = $products->take(5)->map(fn ($p) => sprintf(
            '- %s (₱%s, stock %d, id %d)',
            $p->name,
            number_format((float) $p->price, 2),
            $p->stock,
            $p->id,
        ))->implode("\n");

        $actionSummary = json_encode($actionResult, JSON_UNESCAPED_UNICODE);

        $system = <<<'PROMPT'
You are a friendly RepoMart voice shopping assistant in the Philippines.
Reply in 1-2 short sentences using the same language as the user (English, Tagalog, or Bisaya).
Be conversational. Suggest a clear next step (view product, add to cart, continue checkout).
Do not list more than 3 product names. Never mention internal IDs to the user.
PROMPT;

        $user = <<<USER
User said: {$nlp['original_text']}
Language: {$nlp['language']}
Intent: {$nlp['intent']}
Products found:
{$productLines}
Action result: {$actionSummary}
USER;

        try {
            $client = OpenAI::client($apiKey);
            $response = $client->chat()->create([
                'model' => config('voice-assistant.nlp_model', 'gpt-4o-mini'),
                'max_tokens' => config('voice-assistant.reply_max_tokens', 120),
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
            ]);

            $reply = trim((string) ($response->choices[0]->message->content ?? ''));

            return $reply !== '' ? $reply : $this->fallbackReply($nlp, $products, $actionResult);
        } catch (\Throwable) {
            return $this->fallbackReply($nlp, $products, $actionResult);
        }
    }

    /**
     * @param  array<string, mixed>  $nlp
     * @param  Collection<int, \App\Models\Product>  $products
     * @param  array<string, mixed>  $actionResult
     */
    private function fallbackReply(array $nlp, Collection $products, array $actionResult): string
    {
        if (($actionResult['requires_login'] ?? false) === true) {
            return 'Please sign in to add items to your cart or continue checkout.';
        }

        if (($actionResult['success'] ?? false) && isset($actionResult['message'])) {
            return (string) $actionResult['message'];
        }

        if ($products->isEmpty()) {
            return 'Sorry, I could not find matching products. Try saying the product name in English or Filipino.';
        }

        $names = $products->take(3)->pluck('name')->implode(', ');

        return "I found {$products->count()} product(s) including {$names}. Would you like to add one to your cart?";
    }
}
