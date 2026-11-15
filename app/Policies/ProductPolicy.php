<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function create(User $user): bool
    {
        return $user->managed_business_id !== null;
    }

    public function update(User $user, Product $product): bool
    {
        return $user->managed_business_id === $product->printing_business_id;
    }
}
