<?php

namespace App\Services\VoiceAssistant;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use OpenAI;
use RuntimeException;

class TranscriptionService
{
    public function transcribe(UploadedFile $audio): string
    {
        $apiKey = config('voice-assistant.openai_api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('Voice assistant is not configured. Set OPENAI_API_KEY.');
        }

        $client = OpenAI::client($apiKey);
        $model = config('voice-assistant.transcribe_model');
        $tempPath = $this->prepareAudioPath($audio);

        $fallback = (string) config('voice-assistant.transcribe_fallback_model');

        try {
            $text = $this->requestTranscription($client, $model, $tempPath);

            if ($this->containsHanScript($text) && $model !== $fallback) {
                Log::warning('Voice transcript used Chinese script, retrying for Filipino or Bisaya', [
                    'model' => $model,
                ]);

                $text = $this->requestTranscription($client, $fallback, $tempPath);
            }

            return $this->withoutHanScript($text);
        } catch (\Throwable $e) {
            Log::warning('Voice transcribe primary model failed, trying fallback', [
                'model' => $model,
                'error' => $e->getMessage(),
            ]);

            return $this->withoutHanScript($this->requestTranscription($client, $fallback, $tempPath));
        } finally {
            @unlink($tempPath);
        }
    }

    private function prepareAudioPath(UploadedFile $audio): string
    {
        $extension = $this->resolveExtension($audio);
        $tempPath = sys_get_temp_dir().'/'.uniqid('voice_', true).'.'.$extension;

        if (! copy($audio->getRealPath(), $tempPath)) {
            throw new RuntimeException('Could not prepare audio for transcription.');
        }

        return $tempPath;
    }

    private function resolveExtension(UploadedFile $audio): string
    {
        $extension = strtolower((string) $audio->getClientOriginalExtension());

        if ($extension !== '') {
            return $extension;
        }

        return match ($audio->getMimeType()) {
            'audio/mp4', 'audio/x-m4a' => 'mp4',
            'audio/ogg' => 'ogg',
            'audio/mpeg', 'audio/mp3' => 'mp3',
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/flac' => 'flac',
            default => 'webm',
        };
    }

    private function requestTranscription(\OpenAI\Client $client, string $model, string $path): string
    {
        $response = $client->audio()->transcribe([
            'model' => $model,
            'file' => fopen($path, 'r'),
            'response_format' => 'json',
            ...$this->hints(),
        ]);

        return trim((string) ($response->text ?? ''));
    }

    /**
     * @return array{language: string, prompt: string}
     */
    public function hints(): array
    {
        return [
            'language' => (string) config('voice-assistant.transcribe_language', 'tl'),
            'prompt' => (string) config('voice-assistant.transcribe_prompt'),
        ];
    }

    public function containsHanScript(string $text): bool
    {
        return (bool) preg_match('/\p{Han}/u', $text);
    }

    public function withoutHanScript(string $text): string
    {
        $stripped = preg_replace('/\p{Han}+/u', ' ', $text) ?? $text;
        $stripped = preg_replace('/\s+/u', ' ', $stripped) ?? $stripped;

        return trim($stripped);
    }
}
