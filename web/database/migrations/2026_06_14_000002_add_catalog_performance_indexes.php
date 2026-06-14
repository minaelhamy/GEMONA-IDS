<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'id'], 'products_status_id_idx');
            $table->index(['status', 'product_category_id'], 'products_status_category_idx');
            $table->index(['status', 'product_brand_id'], 'products_status_brand_idx');
            $table->index(['status', 'variation_price'], 'products_status_price_idx');
            $table->index(['product_category_id', 'product_brand_id'], 'products_category_brand_idx');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->index(['status', 'parent_id'], 'product_categories_status_parent_idx');
        });

        Schema::table('product_brands', function (Blueprint $table) {
            $table->index(['status', 'id'], 'product_brands_status_id_idx');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->index(['product_id', 'product_attribute_id', 'product_attribute_option_id'], 'product_variations_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropIndex('product_variations_lookup_idx');
        });

        Schema::table('product_brands', function (Blueprint $table) {
            $table->dropIndex('product_brands_status_id_idx');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropIndex('product_categories_status_parent_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_id_idx');
            $table->dropIndex('products_status_category_idx');
            $table->dropIndex('products_status_brand_idx');
            $table->dropIndex('products_status_price_idx');
            $table->dropIndex('products_category_brand_idx');
        });
    }
};
