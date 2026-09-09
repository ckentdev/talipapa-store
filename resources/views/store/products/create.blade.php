@extends('layouts.store')

@section('title', 'Product Details')

@section('content')
    @include('store.products._form', [
        'categories' => $categories,
        'mode' => 'create',
    ])
@endsection
