<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProductRequest;
use App\Models\CatalogueOptions;
use App\Models\PrintingBusiness;
use App\Models\Product;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WorkspaceController
{
    public function index(Request $request): View
    {
        Gate::authorize('create', Product::class);
        $business = $request->user()->managedBusiness;

        return view('workspace.index', [
            'business' => $business,
            'products' => $business->products()->withCount('variants')->orderByDesc('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Product::class);

        return $this->form(new Product(['status' => 'draft']));
    }

    public function store(SaveProductRequest $request): RedirectResponse
    {
        return $this->save($request, $request->user()->managedBusiness);
    }

    public function show(Product $product): View
    {
        Gate::authorize('update', $product);

        return view('catalogue.show', ['product' => $product->load('business', 'variants'), 'internal' => true]);
    }

    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        return $this->form($product);
    }

    public function update(SaveProductRequest $request, Product $product): RedirectResponse
    {
        return $this->save($request, $request->user()->managedBusiness, $product);
    }

    private function form(Product $product): View
    {
        return view('workspace.form', [
            'product' => $product->load('variants'),
            'categories' => CatalogueOptions::CATEGORIES,
            'statuses' => Product::STATUSES,
        ]);
    }

    private function save(SaveProductRequest $request, PrintingBusiness $business, ?Product $product = null): RedirectResponse
    {
        $fields = $request->safe()->except('variants');
        $variants = $request->validated('variants');

        try {
            $product = DB::transaction(function () use ($business, $product, $fields, $variants) {
                if ($product === null) {
                    $product = $business->products()->create($fields);
                } else {
                    $product = $business->products()->lockForUpdate()->findOrFail($product->id);
                    $product->update($fields);
                    $product->variants()->delete();
                }

                $product->variants()->createMany($variants);

                return $product;
            }, 3);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['sku' => 'This SKU is already in use by your business.']);
        }

        return to_route('workspace.show', $product)->with('success', 'Offering saved.');
    }
}
