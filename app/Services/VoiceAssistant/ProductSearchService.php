<?php

namespace App\Services\VoiceAssistant;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductSearchService
{
    /**
     * @param  array<string, mixed>|null  $nlp
     */
    public function paginate(Request $request, ?array $nearbyStoreIds, int $perPage = 16, ?array $nlp = null): LengthAwarePaginator
    {
        $query = $this->baseQuery($request, $nearbyStoreIds);

        if (! $request->filled('q')) {
            return $query->latest()->paginate($perPage)->withQueryString();
        }

        $text = $request->string('q')->toString();
        $terms = $this->termsFromNlp($nlp, $text);
        $units = $this->unitsFromNlp($nlp, $text);

        return $this->paginateScored($query, $terms, $units, $perPage, (int) $request->input('page', 1), $request->query(), $nlp ?? []);
    }

    /**
     * @param  array<string, mixed>  $nlp
     * @return Collection<int, Product>
     */
    public function searchFromNlp(array $nlp, ?array $nearbyStoreIds = null, ?int $limit = null): Collection
    {
        $limit ??= (int) config('voice-assistant.voice_results_limit', 8);

        $original = (string) ($nlp['original_text'] ?? '');
        $terms = $this->termsFromNlp($nlp, $original);
        $normalizedUnits = $this->unitsFromNlp($nlp, $original);

        $query = Product::query()
            ->available()
            ->with(['store', 'category']);

        if ($nearbyStoreIds !== null) {
            $query->whereIn('store_profile_id', $nearbyStoreIds);
        }

        return $this->scoreAndTake($query, $terms, $normalizedUnits, $limit, $nlp);
    }

    public function baseQuery(Request $request, ?array $nearbyStoreIds): Builder
    {
        return Product::query()
            ->available()
            ->with(['store', 'category'])
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('store'), fn ($q) => $q->where('store_profile_id', $request->integer('store')))
            ->when($nearbyStoreIds !== null, fn ($q) => $q->whereIn('store_profile_id', $nearbyStoreIds));
    }

    /**
     * @param  array<int, string>  $terms
     * @param  array<int, string>  $units
     * @param  array<string, mixed>  $nlp
     */
    private function paginateScored(Builder $baseQuery, array $terms, array $units, int $perPage, int $page, array $queryParams, array $nlp = []): LengthAwarePaginator
    {
        $scored = $this->scoreAndTake(clone $baseQuery, $terms, $units, 500, $nlp);
        $total = $scored->count();
        $items = $scored->forPage($page, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => $queryParams],
        );
    }

    /**
     * Match product titles first. Descriptions are long and mention ingredients
     * in passing, so they are only used when no title or category matches.
     *
     * @param  array<int, string>  $terms
     * @return Collection<int, Product>
     */
    private function loadCandidates(Builder $query, array $terms, array $nlp = []): Collection
    {
        $terms = array_values(array_unique(array_filter($terms)));

        if ($terms === []) {
            return $query->limit(200)->get();
        }

        $ordered = $this->orderCandidates($this->matchTerms(clone $query, $terms, false), $terms, $nlp);
        $primary = $ordered->limit(200)->get();

        if ($primary->isNotEmpty()) {
            return $primary;
        }

        return $this->orderCandidates($this->matchTerms(clone $query, $terms, true), $terms, $nlp)
            ->limit(80)
            ->get();
    }

    /**
     * @param  array<int, string>  $terms
     * @param  array<string, mixed>  $nlp
     */
    private function orderCandidates(Builder $query, array $terms, array $nlp): Builder
    {
        $phrase = collect($terms)->sortByDesc(fn (string $term) => strlen($term))->first();

        if (is_string($phrase) && $phrase !== '') {
            $query->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ['%'.$phrase.'%']);
        }

        if (($nlp['price_intent'] ?? null) === 'cheap') {
            $query->orderBy('price');
        }

        return $query;
    }

    /**
     * @param  array<int, string>  $terms
     */
    private function matchTerms(Builder $query, array $terms, bool $includeDescription): Builder
    {
        return $query->where(function (Builder $outer) use ($terms, $includeDescription) {
            foreach ($terms as $term) {
                $like = '%'.$term.'%';
                $outer->orWhere('name', 'like', $like)
                    ->orWhereHas('category', fn (Builder $cat) => $cat->where('name', 'like', $like));

                if ($includeDescription) {
                    $outer->orWhere('description', 'like', $like);
                }
            }
        });
    }

    /**
     * @param  array<int, string>  $terms
     * @param  array<int, string>  $units
     * @param  array<string, mixed>  $nlp
     * @return Collection<int, Product>
     */
    private function scoreAndTake(Builder $query, array $terms, array $units, int $limit, array $nlp = []): Collection
    {
        $products = $this->loadCandidates($query, $terms, $nlp);

        $scored = $products
            ->map(fn (Product $product) => [
                'product' => $product,
                'score' => $this->scoreProduct($product, $terms, $units, $nlp),
            ])
            ->filter(fn (array $row) => $row['score'] > 0);

        if (($nlp['price_intent'] ?? null) === 'cheap' && $scored->isNotEmpty()) {
            $maxPrice = (float) $scored->max(fn (array $row) => (float) $row['product']->price);
            $maxPrice = max($maxPrice, 0.01);

            $scored = $scored->map(function (array $row) use ($maxPrice) {
                $row['score'] += (1 - ((float) $row['product']->price / $maxPrice)) * 30;

                return $row;
            });
        }

        return $scored
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('product')
            ->values();
    }

    /**
     * @param  array<int, string>  $terms
     * @param  array<int, string>  $units
     * @param  array<string, mixed>  $nlp
     */
    private function scoreProduct(Product $product, array $terms, array $units, array $nlp = []): float
    {
        if ($this->isExcluded($product, $nlp) || $this->missesCanonicalProduct($product, $nlp)) {
            return 0.0;
        }

        $name = Str::lower($product->name);
        $description = Str::lower((string) $product->description);
        $category = Str::lower((string) $product->category?->name);
        $haystack = $name.' '.$description;
        $score = 0.0;

        foreach ($terms as $term) {
            $term = Str::lower(trim($term));

            if ($term === '') {
                continue;
            }

            if ($name === $term) {
                $score += 100;
            } elseif (str_contains($name, $term)) {
                $score += 50;
            }

            if (str_contains($description, $term)) {
                $score += 20;
            }

            if (str_contains($category, $term)) {
                $score += 15;
            }
        }

        foreach ($units as $unit) {
            if ($this->productMatchesUnit($name, $unit)) {
                $score += 35;
            }
        }

        $brand = Str::lower(trim((string) ($nlp['brand'] ?? '')));
        if ($brand !== '' && str_contains($haystack, $brand)) {
            $score += 45;
        }

        foreach ($this->dietaryBoostTerms($nlp) as $dietTerm) {
            if (str_contains($haystack, $dietTerm)) {
                $score += 40;
            }
        }

        return $score;
    }

    /**
     * @param  array<string, mixed>  $nlp
     */
    private function isExcluded(Product $product, array $nlp): bool
    {
        $exclusions = array_map(
            fn ($item) => Str::lower(trim((string) $item)),
            (array) ($nlp['exclusions'] ?? []),
        );
        $dietary = array_map(
            fn ($item) => Str::lower(trim((string) $item)),
            (array) ($nlp['dietary'] ?? []),
        );

        $haystack = Str::lower($product->name.' '.(string) $product->description);

        if (in_array('cow', $exclusions, true) || array_intersect($dietary, ['vegan', 'plant-based', 'non-cow', 'non-dairy']) !== []) {
            if ($this->looksLikeCowDairy($haystack)) {
                return true;
            }
        }

        foreach ($exclusions as $exclusion) {
            if ($exclusion === '' || $exclusion === 'cow') {
                continue;
            }

            if (str_contains($haystack, $exclusion) && ! $this->looksLikePlantMilk($haystack)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $nlp
     */
    private function missesCanonicalProduct(Product $product, array $nlp): bool
    {
        $canonical = Str::lower(trim((string) ($nlp['product'] ?? '')));

        if ($canonical === '') {
            return false;
        }

        $haystack = Str::lower($product->name.' '.(string) $product->category?->name);
        $terms = $this->englishMatchTerms($canonical);

        foreach (array_unique($terms) as $term) {
            $term = Str::lower(trim((string) $term));
            if ($term !== '' && str_contains($haystack, $term)) {
                return false;
            }
        }

        return true;
    }

    private function looksLikeCowDairy(string $haystack): bool
    {
        $isMilk = str_contains($haystack, 'milk')
            || str_contains($haystack, 'gatas')
            || str_contains($haystack, 'dairy');

        if (! $isMilk) {
            return false;
        }

        if ($this->looksLikePlantMilk($haystack)) {
            return false;
        }

        foreach (config('voc.cow_dairy_terms', []) as $term) {
            if (str_contains($haystack, Str::lower((string) $term))) {
                return true;
            }
        }

        return true;
    }

    private function looksLikePlantMilk(string $haystack): bool
    {
        foreach (config('voc.plant_milk_terms', []) as $term) {
            if (str_contains($haystack, Str::lower((string) $term))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $nlp
     * @return array<int, string>
     */
    private function dietaryBoostTerms(array $nlp): array
    {
        $terms = [];

        foreach ((array) ($nlp['dietary'] ?? []) as $diet) {
            $diet = Str::lower(trim((string) $diet));
            if ($diet !== '') {
                $terms[] = $diet;
            }
        }

        if (array_intersect($terms, ['vegan', 'plant-based', 'non-cow', 'non-dairy']) !== []) {
            $terms = array_merge($terms, array_map('strval', config('voc.plant_milk_terms', [])));
        }

        return array_values(array_unique($terms));
    }

    /**
     * @param  array<string, mixed>|null  $nlp
     * @return array<int, string>
     */
    private function termsFromNlp(?array $nlp, string $text): array
    {
        if ($nlp !== null) {
            $seeds = array_merge(
                array_map('strval', $nlp['keywords'] ?? []),
                $this->tokenize((string) ($nlp['product'] ?? '')),
                $this->tokenize((string) ($nlp['brand'] ?? '')),
                array_map('strval', $nlp['dietary'] ?? []),
            );

            if ($seeds === [] || implode('', $seeds) === '') {
                $seeds[] = $text;
            }

            return $this->filterSearchTerms($this->expandTerms($seeds));
        }

        return $this->filterSearchTerms($this->expandTerms([$text]));
    }

    /**
     * @param  array<string, mixed>|null  $nlp
     * @return array<int, string>
     */
    private function unitsFromNlp(?array $nlp, string $text): array
    {
        $units = $nlp !== null ? array_map('strval', $nlp['units'] ?? []) : [];

        return $this->normalizeUnits(array_merge($units, $this->normalizeUnitsFromText($text)));
    }

    /**
     * @param  array<int, string>  $terms
     * @return array<int, string>
     */
    private function filterSearchTerms(array $terms): array
    {
        $stopwords = config('voc.stopwords', []);
        $price = array_merge(config('voc.price_cheap', []), config('voc.price_premium', []));
        $priceTokens = array_map(fn ($item) => Str::lower((string) $item), $price);

        return array_values(array_filter($terms, function (string $term) use ($stopwords, $priceTokens) {
            $term = Str::lower(trim($term));

            if ($term === '' || strlen($term) < 2 || is_numeric($term)) {
                return false;
            }

            if (in_array($term, $stopwords, true) || in_array($term, $priceTokens, true)) {
                return false;
            }

            return ! in_array($term, ['ml', 'kg', 'pcs', 'cow', 'baka', 'liter', 'litro', 'liters', 'litres', 'kilo'], true);
        }));
    }

    private function productMatchesUnit(string $productName, string $unit): bool
    {
        $unit = Str::lower(trim($unit));
        $patterns = $this->unitPatterns($unit);

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $productName)) {
                return true;
            }
        }

        return str_contains($productName, $unit);
    }

    /**
     * @return array<int, string>
     */
    private function unitPatterns(string $unit): array
    {
        if (preg_match('/^(\d+(?:\.\d+)?)\s*l$/i', $unit, $m)) {
            $n = $m[1];

            return [
                '/\b'.preg_quote($n, '/').'\s*l\b/i',
                '/\b'.preg_quote($n, '/').'\s*liter/i',
                '/\b'.preg_quote($n, '/').'\s*litro/i',
                '/\b'.preg_quote((string) ((int) ((float) $n * 1000)), '/').'\s*ml\b/i',
            ];
        }

        if (preg_match('/^(\d+(?:\.\d+)?)\s*kg$/i', $unit, $m)) {
            $n = $m[1];

            return [
                '/\b'.preg_quote($n, '/').'\s*kg\b/i',
                '/\b'.preg_quote($n, '/').'\s*kilo/i',
            ];
        }

        if (preg_match('/^(\d+(?:\.\d+)?)\s*ml$/i', $unit, $m)) {
            $n = (int) ((float) $m[1]);

            return [
                '/\b'.preg_quote((string) $n, '/').'\s*ml\b/i',
                '/\b'.preg_quote((string) ($n / 1000), '/').'\s*l\b/i',
            ];
        }

        return ['/'.preg_quote($unit, '/').'/i'];
    }

    /**
     * @return array<int, string>
     */
    private function englishMatchTerms(string $canonical): array
    {
        $canonical = Str::lower(trim($canonical));
        $terms = array_merge(
            [$canonical],
            array_map('strval', config('voice-assistant.synonyms.'.$canonical, [])),
            array_map('strval', config('voc.catalog_aliases.'.$canonical, [])),
        );

        return array_values(array_unique(array_filter(array_map(
            fn ($term) => Str::lower(trim((string) $term)),
            $terms,
        ))));
    }

    /**
     * Replace Bisaya and Tagalog grocery words with the English catalog name
     * before tokenizing, so "tuyo" searches "soy sauce" as one phrase.
     *
     * @param  array<int, string>  $inputs
     * @return array<int, string>
     */
    public function expandTerms(array $inputs): array
    {
        $terms = [];
        $map = config('voc.canonical_products', []);
        $phrases = array_keys($map);
        usort($phrases, fn (string $a, string $b) => strlen($b) <=> strlen($a));

        foreach ($inputs as $input) {
            $text = ' '.Str::lower((string) $input).' ';

            foreach ($phrases as $phrase) {
                $pattern = '/(?<![\p{L}\p{N}])'.preg_quote($phrase, '/').'(?![\p{L}\p{N}])/u';

                if (! preg_match($pattern, $text)) {
                    continue;
                }

                $english = Str::lower((string) $map[$phrase]);
                $terms[] = $english;

                if ($this->keepSourceTerm($phrase, $english)) {
                    $terms[] = $phrase;
                }

                foreach ($this->synonymsFor($english) as $synonym) {
                    $terms[] = $synonym;
                }

                $text = (string) preg_replace($pattern, ' ', $text);
            }

            foreach ($this->tokenize($text) as $token) {
                $terms[] = $token;

                foreach ($this->synonymsFor($token) as $synonym) {
                    $terms[] = $synonym;
                }
            }
        }

        return array_values(array_unique(array_filter(
            $terms,
            fn ($term) => is_string($term) && strlen($term) >= 2,
        )));
    }

    private function keepSourceTerm(string $phrase, string $english): bool
    {
        if ($phrase === $english) {
            return true;
        }

        $aliases = array_map(
            fn ($alias) => Str::lower((string) $alias),
            config('voc.catalog_aliases.'.$english, []),
        );

        return in_array($phrase, $aliases, true);
    }

    /**
     * @return array<int, string>
     */
    private function synonymsFor(string $term): array
    {
        return array_map('strval', config('voice-assistant.synonyms.'.Str::lower($term), []));
    }

    /**
     * @return array<int, string>
     */
    public function tokenize(string $text): array
    {
        $text = Str::lower($text);
        preg_match_all('/[\p{L}\p{N}]+/u', $text, $matches);

        return $matches[0] ?? [];
    }

    /**
     * @param  array<int, string>  $units
     * @return array<int, string>
     */
    public function normalizeUnits(array $units): array
    {
        $normalized = [];

        foreach ($units as $unit) {
            $normalized = array_merge($normalized, $this->normalizeUnitsFromText((string) $unit));
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @return array<int, string>
     */
    public function normalizeUnitsFromText(string $text): array
    {
        $text = Str::lower($text);
        $units = [];

        if (preg_match_all('/(\d+(?:\.\d+)?)\s*(l|liter|liters|litro|litres|ml|kg|kilo|kilogram|g|gram|grams|oz|ounce|ounces|pcs|pc|piece|pieces)\b/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $amount = (float) $match[1];
                $rawUnit = Str::lower($match[2]);
                $canonical = config('voice-assistant.unit_aliases.'.$rawUnit, $rawUnit);

                if ($canonical === 'ml' && $amount >= 1000) {
                    $units[] = $this->formatAmount($amount / 1000).'L';
                    $units[] = $this->formatAmount($amount).'ml';
                } elseif ($canonical === 'l') {
                    $units[] = $this->formatAmount($amount).'L';
                    $units[] = $this->formatAmount($amount * 1000).'ml';
                } elseif ($canonical === 'kg') {
                    $units[] = $this->formatAmount($amount).'kg';
                } else {
                    $units[] = $this->formatAmount($amount).$canonical;
                }
            }
        }

        return array_values(array_unique($units));
    }

    private function formatAmount(float $amount): string
    {
        if (abs($amount - round($amount)) < 0.0001) {
            return (string) (int) round($amount);
        }

        return rtrim(rtrim(sprintf('%.4F', $amount), '0'), '.');
    }
}
