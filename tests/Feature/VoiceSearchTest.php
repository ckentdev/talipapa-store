<?php

namespace Tests\Feature;

use App\Services\VoiceAssistant\TranscriptionService;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class VoiceSearchTest extends TestCase
{
    private function fakeAudioUpload(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'voice.webm',
            "\x1A\x45\xDF\xA3".str_repeat("\0", 100),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'voice-assistant.enabled' => true,
            'voice-assistant.search_enabled' => true,
            'voice-assistant.openai_api_key' => 'test-key',
        ]);
    }

    public function test_transcribe_endpoint_returns_disabled_when_search_disabled(): void
    {
        config([
            'voice-assistant.search_enabled' => false,
            'voice-assistant.openai_api_key' => 'test-key',
        ]);

        $this->post(route('voice-search.transcribe'), [
            'audio' => $this->fakeAudioUpload(),
        ])->assertStatus(503);
    }

    public function test_transcribe_endpoint_returns_unconfigured_when_no_api_key(): void
    {
        config(['voice-assistant.openai_api_key' => null]);

        $this->post(route('voice-search.transcribe'), [
            'audio' => $this->fakeAudioUpload(),
        ])->assertStatus(503);
    }

    public function test_transcribe_works_when_voice_assistant_is_disabled(): void
    {
        config([
            'voice-assistant.enabled' => false,
            'voice-assistant.search_enabled' => true,
            'voice-assistant.openai_api_key' => 'test-key',
        ]);

        $mock = Mockery::mock(TranscriptionService::class);
        $mock->shouldReceive('transcribe')
            ->once()
            ->andReturn('rice');

        $this->instance(TranscriptionService::class, $mock);

        $this->post(route('voice-search.transcribe'), [
            'audio' => $this->fakeAudioUpload(),
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('transcript', 'rice');
    }

    public function test_transcribe_endpoint_returns_transcript(): void
    {
        $mock = Mockery::mock(TranscriptionService::class);
        $mock->shouldReceive('transcribe')
            ->once()
            ->andReturn('coca cola');

        $this->instance(TranscriptionService::class, $mock);

        $this->post(route('voice-search.transcribe'), [
            'audio' => $this->fakeAudioUpload(),
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('transcript', 'coca cola');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
