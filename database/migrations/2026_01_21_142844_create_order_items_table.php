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
        Schema::create('order_items', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

    $table->string('product_external_id')->nullable()->index();
    $table->unsignedBigInteger('product_id')->nullable()->index();
    $table->string('title')->nullable();

    $table->decimal('quantity', 12, 3)->default(1);
    $table->decimal('unit_amount', 12, 2)->default(0);
    $table->decimal('total_amount', 12, 2)->default(0);

    $table->json('raw')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
