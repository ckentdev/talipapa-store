<?php

namespace App\Http\Controllers;

use App\Services\VoiceAssistant\TranscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class VoiceSearchController extends Controller
{
    public function __construct(
        private readonly TranscriptionService $transcriptionService,
    ) {}

    public function transcribe(Request $request): JsonResponse
    {
        if (! config('voice-assistant.search_enabled')) {
            return response()->json(['message' => 'Voice search is disabled.'], 503);
        }

        if (empty(config('voice-assistant.openai_api_key'))) {
            return response()->json(['message' => 'Voice search is not configured.'], 503);
        }

        $request->validate([
            'audio' => ['required', 'file', 'mimes:webm,ogg,mp4,wav,mpeg,mp3', 'max:5120'],
        ]);

        try {
            $transcript = $this->transcriptionService->transcribe($request->file('audio'));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }

        if ($transcript === '') {
            return response()->json(['message' => 'No speech detected. Please try again.'], 422);
        }

        return response()->json(['transcript' => $transcript]);
    }
}
