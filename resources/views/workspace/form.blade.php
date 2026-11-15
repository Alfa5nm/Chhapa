@extends('layout')
@section('title', $product->exists ? 'Edit offering' : 'Add offering')
@section('content')
<a class="back" href="{{ route('workspace.index') }}">← Business workspace</a>
<h1>{{ $product->exists ? 'Edit offering' : 'Add offering' }}</h1>
@if ($errors->any())
    <section class="notice error" role="alert" aria-label="Validation errors">
        <strong>Check the offering details</strong>
        <ul>
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </section>
@endif
@php
    $categoryOptions = old('category', $product->category);
    $rows = old('variants', $product->variants->toArray());
    if (!is_array($rows) || $rows === []) {
        $rows = [[]];
    }
    $rows = array_values(array_slice($rows, 0, 25));
    $displayValue = function ($value) {
        return is_scalar($value) ? $value : '';
    };
@endphp
<form class="stack" id="product-form" method="post"
    action="{{ $product->exists ? route('workspace.update', $product) : route('workspace.store') }}"
    data-categories="{{ json_encode($categories, JSON_THROW_ON_ERROR) }}">
    @csrf
    @if ($product->exists)
        @method('PUT')
    @endif
    <section class="panel stack">
        <h2>Product details</h2>
        <div class="form-grid">
            <label>Product name <input name="name" maxlength="120" value="{{ $displayValue(old('name', $product->name)) }}" required></label>
            <label>Business SKU <input name="sku" maxlength="40" value="{{ $displayValue(old('sku', $product->sku)) }}" required></label>
            <label>Product type
                <select name="category" required>
                    <option value="">Choose a product type</option>
                    @foreach (array_keys($categories) as $category)
                        <option @selected($categoryOptions === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </label>
            <label>Publication status
                <select name="status" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $product->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <label>Description <textarea name="description" rows="3" maxlength="2000" required>{{ $displayValue(old('description', $product->description)) }}</textarea></label>
    </section>
    <section class="stack">
        <h2>Print variants</h2>
        <div id="variants" class="stack">
            @foreach ($rows as $index => $variant)
                @include('workspace.variant', ['variant' => is_array($variant) ? $variant : []])
            @endforeach
        </div>
        <template id="variant-template">
            @include('workspace.variant', ['index' => 0, 'variant' => []])
        </template>
        <div><button type="button" class="button button-secondary" id="add-variant">Add variant</button></div>
    </section>
    <div><button class="button">Save offering</button></div>
</form>
@endsection
