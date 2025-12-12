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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();

            // Заголовок карточки
            $table->string('title');

            // Описание
            $table->text('description')->nullable();

            // Путь к картинке (storage/cards/...)
            $table->string('image_path')->nullable();

            // Язык карточки
            $table->string('locale', 5)->default('en');

            // Порядок отображения
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
