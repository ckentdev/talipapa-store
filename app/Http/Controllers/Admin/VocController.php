<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VocSource;
use App\Http\Controllers\Controller;
use App\Services\Voc\QueryAttributeExtractor;
use App\Services\Voc\VocInsightsService;
use App\Services\Voc\VocLogger;
use App\Services\VoiceAssistant\ProductSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VocController extends Controller
{
    public function __construct(
        private readonly VocInsightsService $vocInsightsService,
        private readonly QueryAttributeExtractor $queryAttributeExtractor,
        private readonly ProductSearchService $productSearchService,
        private readonly VocLogger $vocLogger,
    ) {}

    public function index(Request $request): View
    {
        $from = $request->date('from')?->startOfDay() ?? now()->subDays(30)->startOfDay();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $insights = $this->vocInsightsService->summarize($from, $to);
        $examples = [
            'barato na tuyo',
            'gatas na dili sa baka',
            'gatas na vegan 300 ml nestle',
        ];

        return view('admin.voc.index', compact('insights', 'examples'));
    }

    public function extract(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:500'],
        ]);

        $nlp = $this->queryAttributeExtractor->extract($validated['q']);
        $products = $this->productSearchService->searchFromNlp($nlp, null, 8);

        $this->vocLogger->log($request, $nlp, $products->count(), VocSource::Playground);

        return response()->json([
            'original_text' => $nlp['original_text'],
            'language' => $nlp['language'],
            'intent' => $nlp['intent'],
            'product' => $nlp['product'],
            'brand' => $nlp['brand'],
            'dietary' => $nlp['dietary'],
            'price_intent' => $nlp['price_intent'],
            'exclusions' => $nlp['exclusions'],
            'units' => $nlp['units'],
            'keywords' => $nlp['keywords'],
            'attributes' => $nlp['attributes'],
            'result_count' => $products->count(),
            'products' => $products->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'store_name' => $product->store?->store_name,
                'url' => route('products.show', $product),
            ])->values(),
        ]);
    }
}
