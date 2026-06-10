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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->decimal('tax_rate', 19, 6)->default(0);
            $table->unsignedTinyInteger('status')->default(\App\Enums\Status::ACTIVE)->comment(\App\Enums\Status::ACTIVE . '=' . trans('statuse.' . \App\Enums\Status::ACTIVE) . ', ' . \App\Enums\Status::INACTIVE . '=' . trans('statuse.' . \App\Enums\Status::INACTIVE));
            $table->string('creator_type',)->nullable();
            $table->bigInteger('creator_id',)->nullable();
            $table->string('editor_type',)->nullable();
            $table->bigInteger('editor_id',)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};