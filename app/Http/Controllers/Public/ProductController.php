<?php

namespace App\Http\Controllers\Public;

use App\Enums\VocSource;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreProfile;
use App\Services\LocationService;
use App\Services\Voc\QueryAttributeExtractor;
use App\Services\Voc\VocLogger;
use App\Services\VoiceAssistant\ProductSearchService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly LocationService $locationService,
        private readonly ProductSearchService $productSearchService,
        private readonly QueryAttributeExtractor $queryAttributeExtractor,
        private readonly VocLogger $vocLogger,
    ) {}

    public function index(Request $request): View|Response
    {
        if ($request->filled('lat') && $request->filled('lng')) {
            $request->session()->put('latitude', (float) $request->input('lat'));
            $request->session()->put('longitude', (float) $request->input('lng'));
        }

        $nlp = null;
        if ($request->filled('q')) {
            $nlp = $this->queryAttributeExtractor->extract($request->string('q')->toString());
        }

        $products = $this->paginateProducts($request, $nlp);

        if ($nlp !== null) {
            $this->vocLogger->log($request, $nlp, $products->total(), VocSource::Search);
        }

        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $stores = StoreProfile::query()->approved()->orderBy('store_name')->get(['id', 'store_name']);

        if ($request->ajax()) {
            return response()->view('public.products._results', compact('products', 'categories', 'stores'));
        }

        return view('public.products.index', compact('products', 'categories', 'stores'));
    }

    /**
     * @param  array<string, mixed>|null  $nlp
     */
    private function paginateProducts(Request $request, ?array $nlp)
    {
        $nearbyStoreIds = null;

        if ($request->boolean('nearby')) {
            $lat = $request->session()->get('latitude');
            $lng = $request->session()->get('longitude');

            if ($lat !== null && $lng !== null) {
                $radius = (float) config('repomart.nearby_radius_km', 10);
                $nearbyStoreIds = $this->locationService
                    ->findNearbyStores((float) $lat, (float) $lng, $radius)
                    ->pluck('id');
            }
        }

        return $this->productSearchService->paginate($request, $nearbyStoreIds, 16, $nlp);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_available && $product->stock > 0, 404);

        $product->load(['store', 'category']);

        return view('public.products.show', compact('product'));
    }
}
