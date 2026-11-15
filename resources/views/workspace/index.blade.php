@extends('layout')
@section('title', 'Business workspace')
@section('content')
<header class="page-heading">
    <div>
        <p class="eyebrow">{{ $business->name }}</p>
        <h1>Business catalogue</h1>
    </div>
    <a class="button" href="{{ route('workspace.create') }}">Add offering</a>
</header>
@if ($products->isEmpty())
    <p class="panel">Your business has no offerings yet. Add a product to start your catalogue.</p>
@else
<div class="table-wrap">
    <table>
        <thead><tr><th scope="col">Offering</th><th scope="col">SKU</th><th scope="col">Type</th><th scope="col">Variants</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <th scope="row"><a href="{{ route('workspace.show', $product) }}">{{ $product->name }}</a></th>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category }}</td>
                    <td>{{ $product->variants_count }}</td>
                    <td><span class="status status-{{ $product->status }}">{{ ucfirst($product->status) }}</span></td>
                    <td><a href="{{ route('workspace.edit', $product) }}" aria-label="Edit {{ $product->name }}">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@include('pagination', ['items' => $products])
@endif
@endsection
