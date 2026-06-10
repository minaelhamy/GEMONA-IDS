<?php

use App\Enums\AnalyticSection;
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
        Schema::create('analytic_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analytic_id')->constrained('analytics');
            $table->string('name');
            $table->longText('data');
            $table->tinyInteger('section')->default(AnalyticSection::HEAD);
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
        Schema::dropIfExists('analytic_sections');
    }
};
