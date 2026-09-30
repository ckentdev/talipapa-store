<?php

namespace Tests\Unit;

use App\Services\VoiceAssistant\TranscriptionService;
use Tests\TestCase;

class TranscriptionServiceTest extends TestCase
{
    public function test_transcription_is_guided_to_filipino_and_bisaya(): void
    {
        $hints = app(TranscriptionService::class)->hints();

        $this->assertSame('tl', $hints['language']);
        $this->assertStringContainsString('tuyo', $hints['prompt']);
        $this->assertStringContainsString('Bisaya', $hints['prompt']);
    }

    public function test_chinese_script_is_removed_so_tuyo_stays_filipino(): void
    {
        $service = app(TranscriptionService::class);

        $this->assertTrue($service->containsHanScript('涂油'));
        $this->assertFalse($service->containsHanScript('tuyo'));
        $this->assertFalse($service->containsHanScript('barato na tuyo'));
        $this->assertSame('tuyo', $service->withoutHanScript('涂油 tuyo'));
        $this->assertSame('', $service->withoutHanScript('涂油'));
    }
}
