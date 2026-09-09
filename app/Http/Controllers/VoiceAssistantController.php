<?php

namespace App\Http\Controllers;

use App\Enums\VocSource;
use App\Models\Product;
use App\Services\LocationService;
use App\Services\Voc\VocLogger;
use App\Services\VoiceAssistant\ProductSearchService;
use App\Services\VoiceAssistant\TranscriptionService;
use App\Services\VoiceAssistant\VoiceActionService;
use App\Services\VoiceAssistant\VoiceNlpService;
use App\Services\VoiceAssistant\VoiceReplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class VoiceAssistantController extends Controller
{
    public function __construct(
        private readonly TranscriptionService $transcriptionService,
        private readonly VoiceNlpService $voiceNlpService,
        private readonly ProductSearchService $productSearchService,
        private readonly VoiceActionService $voiceActionService,
        private readonly VoiceReplyService $voiceReplyService,
        private readonly LocationService $locationService,
        private readonly VocLogger $vocLogger,
    ) {}

    public function process(Request $request): JsonResponse
    {
        if (! config('voice-assistant.enabled')) {
            return response()->json(['message' => 'Voice assistant is disabled.'], 503);
        }

        if (empty(config('voice-assistant.openai_api_key'))) {
            return response()->json(['message' => 'Voice assistant is not configured.'], 503);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
            'audio' => ['nullable', 'file', 'mimes:webm,ogg,mp4,wav,mpeg,mp3', 'max:5120'],
            'context' => ['nullable', 'string', 'in:default,products,cart,checkout'],
            'checkout_step' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $text = trim((string) ($validated['message'] ?? ''));

        if ($request->hasFile('audio')) {
            try {
                $text = $this->transcriptionService->transcribe($request->file('audio'));
            } catch (RuntimeException $e) {
                return response()->json(['message' => $e->getMessage()], 503);
            }
        }

        if ($text === '') {
            return response()->json(['message' => 'No speech detected. Please try again.'], 422);
        }

        $context = (string) ($validated['context'] ?? 'default');
        $nlp = $this->voiceNlpService->extract($text, $context);

        $nearbyStoreIds = $this->resolveNearbyStoreIds($request);
        $products = collect();

        if (in_array($nlp['intent'], ['product_search', 'add_to_cart', 'general_chat'], true)) {
            $products = $this->productSearchService->searchFromNlp($nlp, $nearbyStoreIds);
        }

        $actionResult = $this->voiceActionService->apply($request, $nlp, $products);
        $reply = $this->voiceReplyService->generate($nlp, $products, $actionResult);

        if (in_array($nlp['intent'], ['product_search', 'add_to_cart'], true)) {
            $this->vocLogger->log($request, $nlp, $products->count(), VocSource::Voice);
        }

        return response()->json([
            'transcript' => $text,
            'language' => $nlp['language'],
            'intent' => $nlp['intent'],
            'keywords' => $nlp['keywords'],
            'units' => $nlp['units'],
            'product' => $nlp['product'] ?? null,
            'brand' => $nlp['brand'] ?? null,
            'dietary' => $nlp['dietary'] ?? [],
            'price_intent' => $nlp['price_intent'] ?? null,
            'exclusions' => $nlp['exclusions'] ?? [],
            'attributes' => $nlp['attributes'] ?? [],
            'reply' => $reply,
            'products' => $products->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'stock' => $product->stock,
                'image_url' => $product->imageUrl(),
                'store_name' => $product->store?->store_name,
                'url' => route('products.show', $product),
            ])->values(),
            'action' => $actionResult,
            'requires_login' => (bool) ($actionResult['requires_login'] ?? false),
            'redirect' => $actionResult['redirect'] ?? null,
        ]);
    }

    /**
     * @return array<int, int>|null
     */
    private function resolveNearbyStoreIds(Request $request): ?array
    {
        $lat = $request->session()->get('latitude');
        $lng = $request->session()->get('longitude');

        if ($lat === null || $lng === null) {
            return null;
        }

        $radius = (float) config('repomart.nearby_radius_km', 10);

        return $this->locationService
            ->findNearbyStores((float) $lat, (float) $lng, $radius)
            ->pluck('id')
            ->all();
    }
}
