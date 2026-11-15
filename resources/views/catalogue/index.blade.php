@extends('layout')
@section('content')
<header class="hero">
    <h1>Print catalogue</h1>
    <p>Browse products and printing options from independent print businesses.</p>
</header>
<form class="filters" method="get" action="{{ route('catalogue.index') }}">
    <label>Product type
        <select name="category">
            <option value="">All product types</option>
            @foreach ($categories as $option)
                <option @selected($category === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </label>
    <label>Print business
        <select name="provider">
            <option value="">All print businesses</option>
            @foreach ($businesses as $business)
                <option value="{{ $business->slug }}" @selected($provider === $business->slug)>{{ $business->name }}</option>
            @endforeach
        </select>
    </label>
    <button class="button">Apply filters</button>
</form>
<div class="cards">
    @forelse ($products as $product)
        <article class="product-card">
            <div class="category-band">{{ $product->category }}</div>
            <div class="card-body">
                <p class="muted">{{ $product->business->name }}</p>
                <h2><a href="{{ route('catalogue.show', $product->id) }}">{{ $product->name }}</a></h2>
                <p>{{ Illuminate\Support\Str::limit($product->description, 135) }}</p>
                <a class="card-link" href="{{ route('catalogue.show', $product->id) }}">View options →</a>
            </div>
        </article>
    @empty
        <p class="panel">No published offerings match these filters.</p>
    @endforelse
</div>
@include('pagination', ['items' => $products])
@endsection
