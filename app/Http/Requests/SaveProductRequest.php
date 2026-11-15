<?php

namespace App\Http\Requests;

use App\Models\CatalogueOptions;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product ? Gate::allows('update', $product) : Gate::allows('create', Product::class);
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('sku'))) {
            $this->merge(['sku' => strtoupper(trim($this->input('sku')))]);
        }
    }

    public function rules(): array
    {
        $sku = Rule::unique('products')->where('printing_business_id', $this->user()->managed_business_id);
        if ($product = $this->route('product')) {
            $sku->ignore($product);
        }

        return [
            'printing_business_id' => ['prohibited'],
            'business_id' => ['prohibited'],
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(array_keys(CatalogueOptions::CATEGORIES))],
            'description' => ['required', 'string', 'max:2000'],
            'sku' => ['required', 'string', 'max:40', 'regex:/\A[A-Z0-9][A-Z0-9-]*\z/', $sku],
            'status' => ['required', Rule::in(Product::STATUSES)],
            'variants' => ['required', 'array', 'list', 'min:1', 'max:25'],
            'variants.*' => ['required', 'array:size,material,finish,method,min_quantity,max_quantity,printable_area,unit_price'],
            'variants.*.size' => ['required', 'string', 'max:30'],
            'variants.*.material' => ['required', 'string', 'max:35'],
            'variants.*.finish' => ['required', 'string', 'max:30'],
            'variants.*.method' => ['required', 'string', 'max:40'],
            'variants.*.min_quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'variants.*.max_quantity' => ['required', 'integer', 'gte:variants.*.min_quantity', 'max:1000000'],
            'variants.*.printable_area' => ['required', 'string', 'max:160'],
            'variants.*.unit_price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $seen = [];
            foreach ($this->input('variants') as $index => $variant) {
                if (!CatalogueOptions::supports($this->input('category'), $variant)) {
                    $validator->errors()->add("variants.$index.method", 'Choose a supported size, material, finish and print method combination.');
                }

                $key = implode('|', [$variant['size'], $variant['material'], $variant['finish'], $variant['method']]);
                if (isset($seen[$key])) {
                    $validator->errors()->add("variants.$index.method", 'This option combination already appears on this product.');
                }
                $seen[$key] = true;
            }
        }];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'This SKU is already in use by your business.',
            'variants.*.min_quantity.min' => 'Minimum quantity must be at least 1.',
            'variants.*.max_quantity.gte' => 'Maximum quantity must be at least the minimum quantity.',
        ];
    }
}
