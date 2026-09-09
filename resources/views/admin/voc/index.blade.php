@extends('layouts.admin')

@section('title', 'Voice of Customer')

@section('content')
@php
    $kpis = $insights['kpis'];
    $maxProduct = max(1, (int) ($insights['top_products']->max('count') ?: 1));
    $maxBrand = max(1, (int) ($insights['top_brands']->max('count') ?: 1));
    $maxDiet = max(1, (int) ($insights['dietary']->max('count') ?: 1));
    $maxUnmet = max(1, (int) ($insights['unmet']->max('count') ?: 1));
@endphp

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Voice of Customer</h1>
        <p class="mt-1 text-sm text-gray-500">What shoppers ask for in search and voice — extracted into attributes that drive stocking and pricing decisions.</p>
    </div>
    <form method="GET" action="{{ route('admin.voc.index') }}" class="flex flex-wrap items-end gap-2">
        <div>
            <label class="mb-1 block text-xs text-gray-500">From</label>
            <input type="date" name="from" value="{{ $insights['from']->toDateString() }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-xs text-gray-500">To</label>
            <input type="date" name="to" value="{{ $insights['to']->toDateString() }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
        </div>
        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white">Apply</button>
    </form>
</div>

<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ([
        ['Utterances', $kpis['total'], ''],
        ['Unique queries', $kpis['unique_queries'], ''],
        ['Zero-result', $kpis['zero_results'].' ('.$kpis['zero_result_rate'].'%)', 'text-yellow-600'],
        ['Cheap / barato', $kpis['cheap_share'].'%', 'text-brand-600'],
    ] as [$label, $value, $color])
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold {{ $color }}">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="mb-6 grid gap-4 lg:grid-cols-2">
    @foreach ($insights['decisions'] as $card)
        @php
            $tone = match ($card['tone']) {
                'warning' => 'border-yellow-200 bg-yellow-50',
                'info' => 'border-blue-200 bg-blue-50',
                default => 'border-gray-200 bg-white',
            };
        @endphp
        <div class="rounded-xl border p-4 {{ $tone }}">
            <p class="font-semibold text-gray-900">{{ $card['title'] }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ $card['body'] }}</p>
        </div>
    @endforeach
</div>

<div class="mb-6 grid gap-4 lg:grid-cols-2">
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-4 py-3">
            <h2 class="font-semibold">What to stock</h2>
            <p class="text-xs text-gray-500">Demanded products vs matching SKUs</p>
        </div>
        <div class="space-y-3 p-4">
            @forelse ($insights['top_products'] as $row)
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium capitalize">{{ $row->value }}</span>
                        <span class="text-gray-500">{{ $row->count }} asks · {{ $row->sku_count }} SKUs</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full {{ $row->covered ? 'bg-brand-600' : 'bg-yellow-500' }}" style="width: {{ max(8, (int) round(($row->count / $maxProduct) * 100)) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No product attributes in this range.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-4 py-3">
            <h2 class="font-semibold">Unmet demand</h2>
            <p class="text-xs text-gray-500">Queries that returned no products</p>
        </div>
        <div class="space-y-3 p-4">
            @forelse ($insights['unmet'] as $row)
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium">“{{ $row->query }}”</span>
                        <span class="text-gray-500">{{ $row->count }}</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-yellow-500" style="width: {{ max(8, (int) round(($row->count / $maxUnmet) * 100)) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No zero-result queries in this range.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="mb-6 grid gap-4 lg:grid-cols-2">
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-4 py-3">
            <h2 class="font-semibold">Dietary & constraints</h2>
        </div>
        <div class="space-y-3 p-4">
            @forelse ($insights['dietary'] as $row)
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium capitalize">{{ $row->value }}</span>
                        <span class="text-gray-500">{{ $row->count }}</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-emerald-600" style="width: {{ max(8, (int) round(($row->count / $maxDiet) * 100)) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No dietary attributes yet.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-4 py-3">
            <h2 class="font-semibold">Brand mentions</h2>
        </div>
        <div class="space-y-3 p-4">
            @forelse ($insights['top_brands'] as $row)
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium capitalize">{{ $row->value }}</span>
                        <span class="text-gray-500">{{ $row->count }}</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-blue-600" style="width: {{ max(8, (int) round(($row->count / $maxBrand) * 100)) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No brand mentions yet.</p>
            @endforelse
        </div>
    </div>
</div>

<div
    class="mb-6 rounded-xl border border-gray-200 bg-white"
    data-voc-playground
    data-extract-url="{{ route('admin.voc.extract') }}"
>
    <div class="border-b border-gray-200 px-4 py-3">
        <h2 class="font-semibold">Extract & attribute playground</h2>
        <p class="text-xs text-gray-500">Paste a shopper phrase. The extractor returns product, brand, size, diet, price intent, and exclusions.</p>
    </div>
    <div class="space-y-4 p-4">
        <div class="flex flex-wrap gap-2">
            @foreach ($examples as $example)
                <button type="button" data-voc-example="{{ $example }}" class="rounded-full border border-gray-300 px-3 py-1 text-sm text-gray-700 hover:border-brand-600 hover:text-brand-700">
                    {{ $example }}
                </button>
            @endforeach
        </div>
        <form data-voc-form class="flex flex-col gap-2 sm:flex-row">
            @csrf
            <input
                type="text"
                name="q"
                data-voc-input
                maxlength="500"
                placeholder="e.g. gatas na vegan 300 ml nestle"
                class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
            >
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Extract</button>
        </form>
        <p data-voc-status class="hidden text-sm text-gray-500"></p>
        <div data-voc-results class="hidden space-y-4">
            <div data-voc-chips class="flex flex-wrap gap-2"></div>
            <div data-voc-products class="divide-y divide-gray-100 rounded-lg border border-gray-100"></div>
        </div>
    </div>
</div>

<div class="rounded-xl border border-gray-200 bg-white">
    <div class="border-b border-gray-200 px-4 py-3"><h2 class="font-semibold">Recent utterances</h2></div>
    <div class="divide-y divide-gray-100">
        @forelse ($insights['recent'] as $utterance)
            <div class="px-4 py-3">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <p class="font-medium">“{{ $utterance->original_text }}”</p>
                    <p class="text-xs text-gray-500">{{ $utterance->created_at?->diffForHumans() }} · {{ $utterance->source->value }} · {{ $utterance->result_count }} hits</p>
                </div>
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach ($utterance->attributes as $attribute)
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                            {{ $attribute->type->value }}: {{ $attribute->value }}
                            @if ($attribute->polarity->value === 'exclude')
                                <span class="text-red-600">(exclude)</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="px-4 py-6 text-sm text-gray-500">No utterances logged yet.</p>
        @endforelse
    </div>
</div>
@endsection
