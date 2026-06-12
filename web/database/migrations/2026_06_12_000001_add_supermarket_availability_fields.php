<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'supermarket_available')) {
                $table->boolean('supermarket_available')->default(true)->after('supermarket_candidate_count')->index();
            }
            if (!Schema::hasColumn('products', 'supermarket_available_quantity')) {
                $table->unsignedInteger('supermarket_available_quantity')->nullable()->after('supermarket_available');
            }
        });

        Schema::table('supermarket_product_sources', function (Blueprint $table) {
            if (!Schema::hasColumn('supermarket_product_sources', 'source_available')) {
                $table->boolean('source_available')->default(true)->after('source_category_path')->index();
            }
            if (!Schema::hasColumn('supermarket_product_sources', 'source_available_quantity')) {
                $table->unsignedInteger('source_available_quantity')->nullable()->after('source_available');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supermarket_product_sources', function (Blueprint $table) {
            foreach (['source_available_quantity', 'source_available'] as $column) {
                if (Schema::hasColumn('supermarket_product_sources', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('products', function (Blueprint $table) {
            foreach (['supermarket_available_quantity', 'supermarket_available'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
