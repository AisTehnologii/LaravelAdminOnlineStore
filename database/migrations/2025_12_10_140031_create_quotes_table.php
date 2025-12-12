<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5)->default('en'); // en/ru/ro
            $table->text('text');                      // описание (сам текст цитаты)
            $table->string('author');                  // автор
            $table->string('role')->nullable();        // должность
            $table->integer('position')->default(0);   // порядок вывода в слайдере
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
