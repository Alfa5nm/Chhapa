<?php

namespace App\Http\Controllers;

use App\Models\CatalogueOptions;
use App\Models\PrintingBusiness;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class CatalogueController
{
    public function index(Request $request): View
    {
        $category = $request->query('category');
        $provider = $request->query('provider');
        $categories = array_keys(CatalogueOptions::CATEGORIES);
        $businesses = PrintingBusiness::whereHas('products', fn (Builder $query) => $query->published())
            ->orderBy('name')->get();

        $query = Product::published()->with('business')->orderByDesc('id');
        if (in_array($category, $categories, true)) {
            $query->where('category', $category);
        }
        if (is_string($provider) && $businesses->contains('slug', $provider)) {
            $query->whereHas('business', fn (Builder $query) => $query->where('slug', $provider));
        }

        return view('catalogue.index', [
            'products' => $query->paginate(12)->withQueryString(),
            'businesses' => $businesses,
            'categories' => $categories,
            'category' => $category,
            'provider' => $provider,
        ]);
    }

    public function show(int $id): View
    {
        $product = Product::published()->with('business', 'variants')->findOrFail($id);

        return view('catalogue.show', ['product' => $product, 'internal' => false]);
    }
}
