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
        Schema::create('return_and_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_reason_id')->constrained('return_reasons');
            $table->text('note')->nullable();
            $table->text('reject_reason')->nullable();
            $table->foreignId('order_id');
            $table->foreignId('user_id');
            $table->unsignedBigInteger('order_serial_no');
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_and_refunds');
    }
};
