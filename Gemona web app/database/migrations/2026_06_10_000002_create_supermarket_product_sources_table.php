<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supermarket_product_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('source');
            $table->string('source_product_id');
            $table->string('source_sku')->nullable();
            $table->decimal('source_price', 19, 6)->unsigned()->nullable();
            $table->string('source_currency', 12)->default('EGP');
            $table->text('source_image_url')->nullable();
            $table->text('source_product_url')->nullable();
            $table->json('source_category_path')->nullable();
            $table->json('source_payload')->nullable();
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();

            $table->unique(['source', 'source_product_id']);
            $table->index(['product_id', 'source']);
            $table->index('source_sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supermarket_product_sources');
    }
};
