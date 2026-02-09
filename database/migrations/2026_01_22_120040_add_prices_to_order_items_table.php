<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            // qty (если у тебя его нет — добавим)
            if (!Schema::hasColumn('order_items', 'qty')) {
                $table->decimal('qty', 12, 3)->nullable(); // или integer если нужно
            }

            if (!Schema::hasColumn('order_items', 'unit_price')) {
                $table->decimal('unit_price', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('order_items', 'total_price')) {
                $table->decimal('total_price', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('order_items', 'title')) {
                $table->string('title')->nullable();
            }

            if (!Schema::hasColumn('order_items', 'product_external_id')) {
                $table->string('product_external_id')->nullable()->index();
            }

            if (!Schema::hasColumn('order_items', 'raw')) {
                $table->json('raw')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // откатывать можно по желанию (обычно необязательно)
            // но если хочешь — раскомментируй
            // $table->dropColumn(['qty','unit_price','total_price','title','product_external_id','raw']);
        });
    }
};
