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
        Schema::create('orders', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->string('external_id')->nullable()->unique();
    $table->string('number')->nullable()->index();
    $table->dateTime('ordered_at')->nullable();

    $table->string('customer_name')->nullable();
    $table->string('customer_phone')->nullable();
    $table->string('customer_email')->nullable();

    $table->decimal('grand_total', 12, 2)->default(0);
    $table->string('currency', 8)->default('MDL');

    $table->string('status')->default('new')->index();
    $table->json('raw')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
