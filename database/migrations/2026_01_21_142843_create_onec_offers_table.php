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
        Schema::create('onec_offers', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->string('external_id')->unique();
    $table->string('base_product_external_id')->nullable()->index();
    $table->decimal('price', 12, 2)->default(0);
    $table->decimal('qty', 12, 3)->default(0);
    $table->json('properties')->nullable();
    $table->string('locale', 8)->default('ru')->index();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onec_offers');
    }
};
