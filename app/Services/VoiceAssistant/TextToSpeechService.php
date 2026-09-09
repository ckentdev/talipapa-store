<?php

namespace App\Services\VoiceAssistant;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TextToSpeechService
{
    public function synthesize(string $text): string
    {
        $apiKey = config('voice-assistant.openai_api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('Speech synthesis is not configured.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->withHeaders(['Accept' => 'audio/mpeg'])
            ->post('https://api.openai.com/v1/audio/speech', [
                'model' => config('voice-assistant.tts_model', 'tts-1'),
                'input' => mb_substr(trim($text), 0, 4096),
                'voice' => config('voice-assistant.tts_voice', 'nova'),
                'response_format' => 'mp3',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Speech synthesis failed.');
        }

        return $response->body();
    }
}
