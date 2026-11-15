@extends('layout')
@section('title', $product->name)
@section('content')
<a class="back" href="{{ route($internal ? 'workspace.index' : 'catalogue.index') }}">← {{ $internal ? 'Business workspace' : 'Catalogue' }}</a>
<header class="page-heading">
    <div>
        <p class="eyebrow">{{ $product->business->name }} / {{ $product->category }}</p>
        <h1>{{ $product->name }}</h1>
        <p>{{ $product->description }}</p>
        @if ($internal)
            <p><span class="status status-{{ $product->status }}">{{ ucfirst($product->status) }}</span> <span class="muted">SKU {{ $product->sku }}</span></p>
        @endif
    </div>
    @if ($internal)
        <a class="button" href="{{ route('workspace.edit', $product) }}">Edit offering</a>
    @endif
</header>
<h2>Supported print options</h2>
<div class="cards">
    @foreach ($product->variants as $variant)
        <article class="panel">
            <h3>{{ $variant->size }} · {{ $variant->material }}</h3>
            <dl class="specs">
                <dt>Finish</dt><dd>{{ $variant->finish }}</dd>
                <dt>Printing method</dt><dd>{{ $variant->method }}</dd>
                <dt>Quantity</dt><dd>{{ number_format($variant->min_quantity) }}–{{ number_format($variant->max_quantity) }}</dd>
                <dt>Printable area</dt><dd>{{ $variant->printable_area }}</dd>
            </dl>
            @if ($variant->unit_price === null)
                <p class="price">Quotation required</p>
            @else
                <p class="price">৳{{ number_format((float) $variant->unit_price, 2) }} per unit</p>
                <p class="muted">Indicative price</p>
            @endif
        </article>
    @endforeach
</div>
@endsection
