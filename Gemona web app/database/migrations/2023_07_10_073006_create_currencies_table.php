<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->string('code');
            $table->unsignedTinyInteger('is_cryptocurrency');
            $table->decimal('exchange_rate', 19, 6)->nullable();
            $table->string('creator_type')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->string('editor_type')->nullable();
            $table->unsignedBigInteger('editor_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};