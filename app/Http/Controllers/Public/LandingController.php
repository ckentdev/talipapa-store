<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\StoreProfile;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $featuredStores = StoreProfile::query()
            ->approved()
            ->with('addresses')
            ->withCount(['products' => fn ($query) => $query->where('is_available', true)->where('stock', '>', 0)])
            ->latest('approved_at')
            ->limit(3)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($query) => $query->available()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $popularCategoryHighlights = Category::query()
            ->where('is_active', true)
            ->whereHas('products', fn ($query) => $query->available())
            ->with([
                'products' => fn ($query) => $query
                    ->available()
                    ->with('store')
                    ->latest()
                    ->limit(10),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(3)
            ->get();

        return view('public.landing', compact('featuredStores', 'categories', 'popularCategoryHighlights'));
    }
}
