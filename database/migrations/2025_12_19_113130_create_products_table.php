<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Привязка к ContentSection (как у banners/cards/promo)
            $table->foreignId('section_id')
                ->nullable()
                ->constrained('content_sections')
                ->nullOnDelete();

            $table->string('locale', 5)->default('en');
            $table->unsignedInteger('position')->default(1);
            $table->boolean('is_active')->default(true);

            // Анонс
            $table->string('announce_title')->nullable();
            $table->text('announce_description')->nullable();
            $table->string('announce_image_path')->nullable();

            // Детально
            $table->string('title')->required();
            $table->longText('description')->nullable();
            $table->longText('description_extra')->nullable();

            // Цены
            $table->decimal('price', 12, 2)->nullable();       // цена без скидки
            $table->decimal('sale_price', 12, 2)->nullable();  // цена со скидкой

            $table->timestamps();
            $table->softDeletes();

            $table->index(['section_id', 'locale', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
