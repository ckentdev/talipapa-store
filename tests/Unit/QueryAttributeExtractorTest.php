<?php

namespace Tests\Unit;

use App\Services\Voc\QueryAttributeExtractor;
use Tests\TestCase;

class QueryAttributeExtractorTest extends TestCase
{
    private QueryAttributeExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = app(QueryAttributeExtractor::class);
    }

    public function test_extracts_cheap_tuyo(): void
    {
        $nlp = $this->extractor->extract('barato na tuyo');

        $this->assertSame('tuyo', $nlp['product']);
        $this->assertSame('cheap', $nlp['price_intent']);
        $this->assertContains('tuyo', $nlp['keywords']);
        $this->assertContains('dried fish', $nlp['keywords']);
        $this->assertNotContains('barato', $nlp['keywords']);
        $this->assertTrue($this->hasAttribute($nlp, 'product', 'tuyo', 'include'));
        $this->assertTrue($this->hasAttribute($nlp, 'price_intent', 'cheap', 'include'));
    }

    public function test_extracts_non_cow_milk_exclusion(): void
    {
        $nlp = $this->extractor->extract('gatas na dili sa baka');

        $this->assertSame('milk', $nlp['product']);
        $this->assertContains('cow', $nlp['exclusions']);
        $this->assertContains('non-cow', $nlp['dietary']);
        $this->assertSame('Bisaya', $nlp['language']);
        $this->assertTrue($this->hasAttribute($nlp, 'exclusion', 'cow', 'exclude'));
        $this->assertNotContains('cow', $nlp['keywords']);
        $this->assertNotContains('baka', $nlp['keywords']);
    }

    public function test_extracts_vegan_nestle_300ml_milk(): void
    {
        $nlp = $this->extractor->extract('gatas na vegan 300 ml nestle');

        $this->assertSame('milk', $nlp['product']);
        $this->assertSame('nestle', $nlp['brand']);
        $this->assertContains('vegan', $nlp['dietary']);
        $this->assertContains('300ml', $nlp['units']);
        $this->assertContains('cow', $nlp['exclusions']);
        $this->assertTrue($this->hasAttribute($nlp, 'brand', 'nestle', 'include'));
        $this->assertTrue($this->hasAttribute($nlp, 'unit', '300ml', 'include'));
    }

    /**
     * @param  array<string, mixed>  $nlp
     */
    private function hasAttribute(array $nlp, string $type, string $value, string $polarity): bool
    {
        foreach ($nlp['attributes'] as $attribute) {
            if ($attribute['type'] === $type && $attribute['value'] === $value && $attribute['polarity'] === $polarity) {
                return true;
            }
        }

        return false;
    }
}
