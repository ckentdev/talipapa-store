<?php

namespace App\Services\Voc;

use App\Enums\VocSource;
use App\Models\VocUtterance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VocLogger
{
    /**
     * @param  array<string, mixed>  $nlp
     */
    public function log(Request $request, array $nlp, int $resultCount, VocSource|string $source): void
    {
        $text = trim((string) ($nlp['original_text'] ?? ''));

        if (strlen($text) < 2) {
            return;
        }

        $sourceValue = $source instanceof VocSource ? $source->value : $source;
        $actor = $request->user()?->id ?? ('guest:'.($request->hasSession() ? $request->session()->getId() : $request->ip()));
        $minute = now()->format('YmdHi');
        $cacheKey = 'voc_log:'.hash('sha256', $actor.'|'.$this->normalize($text).'|'.$sourceValue.'|'.$minute);

        if (! Cache::add($cacheKey, 1, 70)) {
            return;
        }

        try {
            $utterance = VocUtterance::query()->create([
                'user_id' => $request->user()?->id,
                'source' => $sourceValue,
                'original_text' => $text,
                'language' => $nlp['language'] ?? null,
                'intent' => $nlp['intent'] ?? null,
                'result_count' => max(0, $resultCount),
            ]);

            $seen = [];

            foreach ((array) ($nlp['attributes'] ?? []) as $attribute) {
                $type = (string) ($attribute['type'] ?? '');
                $value = Str::lower(trim((string) ($attribute['value'] ?? '')));
                $polarity = (string) ($attribute['polarity'] ?? 'include');

                if ($type === '' || $value === '') {
                    continue;
                }

                $dedupe = $type.'|'.$value.'|'.$polarity;
                if (isset($seen[$dedupe])) {
                    continue;
                }
                $seen[$dedupe] = true;

                $utterance->attributes()->create([
                    'type' => $type,
                    'value' => $value,
                    'polarity' => $polarity,
                ]);
            }
        } catch (\Throwable) {
            // Search/voice must not fail if analytics logging errors.
        }
    }

    private function normalize(string $text): string
    {
        return Str::lower(trim(preg_replace('/\s+/', ' ', $text) ?? $text));
    }
}
