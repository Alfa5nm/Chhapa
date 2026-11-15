<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = ['name', 'category', 'description', 'sku', 'status'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(PrintingBusiness::class, 'printing_business_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
