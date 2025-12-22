<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('content_sections')
                ->nullOnDelete();

            // как в banners
            $table->unsignedInteger('position')->default(1);
            $table->string('locale', 5)->default('en');

            // контент
            $table->string('title');                // главный заголовок
            $table->string('subtitle')->nullable(); // подзаголовок

            $table->text('description')->nullable();    // описание
            $table->text('description_2')->nullable();  // доп. описание
            $table->text('description_3')->nullable();  // доп. описание

            $table->string('image_path')->nullable(); // картинка
            $table->string('link')->nullable();       // ссылка

            $table->timestamps();

            // индекс как у banners
            $table->index(['section_id', 'locale', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_blocks');
    }
};
