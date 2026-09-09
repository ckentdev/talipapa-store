@extends('layouts.marketplace')

@section('seo')
    <x-seo-meta
        title="Fresh Grocery Delivery from Local Stores"
        description="Shop fresh groceries and everyday essentials from trusted neighborhood talipapa stores online. Browse products, order for delivery, and track every order with Talipapa."
        :canonical="route('landing')"
    />
    <x-seo.landing-schema :faqs="config('landing.faqs')" />
@endsection

@section('hero')
    @include('public.partials._landing_hero')
@endsection

@section('after_hero')
    @include('public.partials._landing_categories', ['categories' => $categories])
    @include('public.partials._landing_popular_products', ['popularCategoryHighlights' => $popularCategoryHighlights])
    @include('public.partials._landing_featured_stores', ['featuredStores' => $featuredStores])
    @include('public.partials._landing_faq')
@endsection
