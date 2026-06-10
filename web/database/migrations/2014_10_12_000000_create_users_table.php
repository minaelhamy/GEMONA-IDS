<?php

use App\Enums\Ask;
use App\Enums\Status;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('device_token')->nullable();
            $table->string('web_token')->nullable();
            $table->unsignedTinyInteger('status')->default(Status::ACTIVE);
            $table->string('country_code')->nullable();
            $table->unsignedTinyInteger('is_guest')->default(Ask::NO);
            $table->decimal('balance', 19, 6)->default(0);
            $table->rememberToken();
            $table->string('creator_type')->nullable();
            $table->bigInteger('creator_id')->nullable();
            $table->string('editor_type')->nullable();
            $table->bigInteger('editor_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};