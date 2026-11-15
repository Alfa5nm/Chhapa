<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printing_businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('managed_business_id')->nullable()
                ->constrained('printing_businesses')->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printing_business_id')->constrained()->restrictOnDelete();
            $table->string('name', 120);
            $table->string('category', 30);
            $table->text('description');
            $table->string('sku', 40);
            $table->string('status', 12)->default('draft');
            $table->timestamps();
            $table->unique(['printing_business_id', 'sku']);
            $table->index(['status', 'category']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size', 30);
            $table->string('material', 35);
            $table->string('finish', 30);
            $table->string('method', 40);
            $table->unsignedInteger('min_quantity');
            $table->unsignedInteger('max_quantity');
            $table->string('printable_area', 160);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');
        Schema::dropIfExists('printing_businesses');
    }
};
