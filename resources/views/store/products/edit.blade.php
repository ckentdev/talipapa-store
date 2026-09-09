@extends('layouts.store')

@section('title', 'Edit Product')

@section('content')
    @include('store.products._form', [
        'product' => $product,
        'categories' => $categories,
        'mode' => 'edit',
    ])
@endsection
