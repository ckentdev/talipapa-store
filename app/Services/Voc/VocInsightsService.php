<?php

namespace App\Services\Voc;

use App\Enums\VocAttributeType;
use App\Models\Product;
use App\Models\VocAttribute;
use App\Models\VocUtterance;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VocInsightsService
{
    /**
     * @return array<string, mixed>
     */
    public function summarize(?CarbonInterface $from = null, ?CarbonInterface $to = null): array
    {
        $from ??= now()->subDays(30)->startOfDay();
        $to ??= now()->endOfDay();

        $utterances = VocUtterance::query()
            ->whereBetween('created_at', [$from, $to])
            ->with('attributes')
            ->latest()
            ->get();

        $total = $utterances->count();
        $zeroResults = $utterances->where('result_count', 0)->count();
        $cheapCount = $this->attributeCount($utterances, VocAttributeType::PriceIntent, 'cheap');
        $uniqueQueries = $utterances->map(fn (VocUtterance $row) => Str::lower(trim($row->original_text)))->unique()->count();

        $topProducts = $this->topAttributes($utterances, VocAttributeType::Product);
        $topBrands = $this->topAttributes($utterances, VocAttributeType::Brand);
        $dietary = $this->topAttributes($utterances, VocAttributeType::Dietary);
        $unmet = $this->unmetQueries($utterances);
        $coverage = $this->productCoverage($topProducts);

        return [
            'from' => $from,
            'to' => $to,
            'kpis' => [
                'total' => $total,
                'unique_queries' => $uniqueQueries,
                'zero_results' => $zeroResults,
                'zero_result_rate' => $total > 0 ? round(($zeroResults / $total) * 100) : 0,
                'cheap_share' => $total > 0 ? round(($cheapCount / $total) * 100) : 0,
                'cheap_count' => $cheapCount,
            ],
            'top_products' => $coverage,
            'top_brands' => $topBrands,
            'dietary' => $dietary,
            'unmet' => $unmet,
            'recent' => $utterances->take(12),
            'decisions' => $this->decisionCards($total, $zeroResults, $cheapCount, $coverage, $dietary, $unmet, $topBrands),
        ];
    }

    /**
     * @param  Collection<int, VocUtterance>  $utterances
     * @return Collection<int, object>
     */
    private function topAttributes(Collection $utterances, VocAttributeType $type, int $limit = 8): Collection
    {
        return $utterances
            ->flatMap(fn (VocUtterance $utterance) => $utterance->attributes)
            ->filter(fn (VocAttribute $attribute) => $attribute->type === $type)
            ->groupBy(fn (VocAttribute $attribute) => $attribute->value)
            ->map(fn (Collection $group, string $value) => (object) [
                'value' => $value,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->take($limit)
            ->values();
    }

    /**
     * @param  Collection<int, VocUtterance>  $utterances
     */
    private function attributeCount(Collection $utterances, VocAttributeType $type, string $value): int
    {
        return $utterances
            ->flatMap(fn (VocUtterance $utterance) => $utterance->attributes)
            ->filter(fn (VocAttribute $attribute) => $attribute->type === $type && $attribute->value === $value)
            ->count();
    }

    /**
     * @param  Collection<int, VocUtterance>  $utterances
     * @return Collection<int, object>
     */
    private function unmetQueries(Collection $utterances, int $limit = 8): Collection
    {
        return $utterances
            ->where('result_count', 0)
            ->groupBy(fn (VocUtterance $row) => Str::lower(trim($row->original_text)))
            ->map(fn (Collection $group, string $text) => (object) [
                'query' => $group->first()->original_text,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->take($limit)
            ->values();
    }

    /**
     * @param  Collection<int, object>  $topProducts
     * @return Collection<int, object>
     */
    private function productCoverage(Collection $topProducts): Collection
    {
        return $topProducts->map(function (object $row) {
            $terms = array_values(array_unique(array_filter([
                $row->value,
                ...config('voice-assistant.synonyms.'.$row->value, []),
            ])));

            $skuCount = 0;
            if ($terms !== []) {
                $skuCount = Product::query()
                    ->available()
                    ->where(function ($query) use ($terms) {
                        foreach ($terms as $term) {
                            $like = '%'.$term.'%';
                            $query->orWhere('name', 'like', $like)
                                ->orWhere('description', 'like', $like);
                        }
                    })
                    ->count();
            }

            return (object) [
                'value' => $row->value,
                'count' => $row->count,
                'sku_count' => $skuCount,
                'covered' => $skuCount > 0,
            ];
        });
    }

    /**
     * @param  Collection<int, object>  $coverage
     * @param  Collection<int, object>  $dietary
     * @param  Collection<int, object>  $unmet
     * @param  Collection<int, object>  $topBrands
     * @return array<int, array{title: string, body: string, tone: string}>
     */
    private function decisionCards(
        int $total,
        int $zeroResults,
        int $cheapCount,
        Collection $coverage,
        Collection $dietary,
        Collection $unmet,
        Collection $topBrands,
    ): array {
        $cards = [];

        foreach ($coverage as $row) {
            if ($row->count >= 1 && $row->sku_count === 0) {
                $cards[] = [
                    'title' => 'Stock '.$row->value,
                    'body' => $row->count.' customer searches asked for '.$row->value.', but the catalog has 0 matching SKUs.',
                    'tone' => 'warning',
                ];
            }
        }

        if ($cheapCount > 0 && $total > 0) {
            $share = round(($cheapCount / $total) * 100);
            $cheapProduct = $coverage->first();
            $focus = $cheapProduct?->value ? ' especially '.$cheapProduct->value : '';
            $cards[] = [
                'title' => 'Price-sensitive demand',
                'body' => $share.'% of Voice-of-Customer queries signal cheap/barato intent'.$focus.'. Keep entry-price packs in stock.',
                'tone' => 'info',
            ];
        }

        $plantDemand = $dietary->first(fn (object $row) => in_array($row->value, ['vegan', 'plant-based', 'non-cow', 'non-dairy'], true));
        if ($plantDemand) {
            $cards[] = [
                'title' => 'Non-cow / plant-based milk',
                'body' => 'Shoppers are asking for vegan or non-cow milk ('.$plantDemand->count.' mentions). A cow-milk-only catalog will miss these orders.',
                'tone' => 'info',
            ];
        }

        $nestle = $topBrands->first(fn (object $row) => str_contains($row->value, 'nestle'));
        if ($nestle) {
            $cards[] = [
                'title' => 'Brand callouts',
                'body' => 'Nestle was mentioned '.$nestle->count.' time(s). Prioritize branded sizes customers name (e.g. 300ml).',
                'tone' => 'info',
            ];
        }

        if ($unmet->isNotEmpty()) {
            $top = $unmet->first();
            $cards[] = [
                'title' => 'Unmet search demand',
                'body' => $zeroResults.' searches returned no products. Top miss: “'.$top->query.'” ('.$top->count.' times).',
                'tone' => 'warning',
            ];
        }

        if ($cards === [] && $total === 0) {
            $cards[] = [
                'title' => 'Waiting for customer voice',
                'body' => 'Search and voice queries will show stocking, pricing, and catalog-gap decisions here. Try the extractor playground below.',
                'tone' => 'neutral',
            ];
        }

        return array_slice($cards, 0, 6);
    }
}
