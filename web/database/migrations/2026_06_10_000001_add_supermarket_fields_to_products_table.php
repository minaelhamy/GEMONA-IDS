<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'source_type')) {
                $table->string('source_type')->default('manual')->after('is_product_quantity_multiply')->index();
            }
            if (!Schema::hasColumn('products', 'external_key')) {
                $table->string('external_key')->nullable()->after('source_type')->unique();
            }
            if (!Schema::hasColumn('products', 'external_image_url')) {
                $table->text('external_image_url')->nullable()->after('external_key');
            }
            if (!Schema::hasColumn('products', 'supermarket_base_price')) {
                $table->decimal('supermarket_base_price', 19, 6)->unsigned()->nullable()->after('external_image_url');
            }
            if (!Schema::hasColumn('products', 'supermarket_margin_percent')) {
                $table->decimal('supermarket_margin_percent', 8, 4)->unsigned()->default(10)->after('supermarket_base_price');
            }
            if (!Schema::hasColumn('products', 'supermarket_candidate_count')) {
                $table->unsignedInteger('supermarket_candidate_count')->default(0)->after('supermarket_margin_percent');
            }
            if (!Schema::hasColumn('products', 'supermarket_synced_at')) {
                $table->timestamp('supermarket_synced_at')->nullable()->after('supermarket_candidate_count');
            }
            if (!Schema::hasColumn('products', 'manual_price_override')) {
                $table->boolean('manual_price_override')->default(false)->after('supermarket_synced_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach ([
                'manual_price_override',
                'supermarket_synced_at',
                'supermarket_candidate_count',
                'supermarket_margin_percent',
                'supermarket_base_price',
                'external_image_url',
                'external_key',
                'source_type',
            ] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
