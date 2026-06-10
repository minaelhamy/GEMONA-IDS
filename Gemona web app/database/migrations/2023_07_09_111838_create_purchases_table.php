<?php

use App\Enums\PurchaseStatus;
use App\Enums\PurchasePaymentStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained();
            $table->timestamp('date');
            $table->string('reference_no')->nullable();
            $table->decimal('tax', 19, 6)->nullable()->default(0);
            $table->decimal('discount', 19, 6)->nullable()->default(0);
            $table->decimal('subtotal', 19, 6);
            $table->decimal('total', 19, 6)->default(0);
            $table->unsignedTinyInteger('status')->default(PurchaseStatus::RECEIVED);
            $table->unsignedTinyInteger('payment_status')->default(PurchasePaymentStatus::PENDING);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};