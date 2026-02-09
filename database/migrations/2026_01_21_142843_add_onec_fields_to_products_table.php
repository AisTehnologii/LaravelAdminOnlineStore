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
        Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
    if (!Schema::hasColumn('products','external_id')) {
        $table->string('external_id')->nullable()->unique()->after('id');
    }
    if (!Schema::hasColumn('products','onec_group_external_id')) {
        $table->string('onec_group_external_id')->nullable()->index();
    }
    if (!Schema::hasColumn('products','sku')) {
        $table->string('sku')->nullable()->index();
    }
    if (!Schema::hasColumn('products','qty')) {
        $table->decimal('qty', 12, 3)->default(0);
    }
    if (!Schema::hasColumn('products','unit')) {
        $table->string('unit', 20)->nullable();
    }
    if (!Schema::hasColumn('products','onec_raw')) {
        $table->json('onec_raw')->nullable();
    }

    // если у тебя цены отдельными полями — можно обновлять price/sale_price напрямую
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
