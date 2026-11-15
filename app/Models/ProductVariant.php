<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'size', 'material', 'finish', 'method', 'min_quantity', 'max_quantity',
        'printable_area', 'unit_price',
    ];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2'];
    }
}
