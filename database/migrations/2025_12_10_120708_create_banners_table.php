<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // номер слайда: 1, 2, 3
            $table->unsignedInteger('position')->default(1);

            // на будущее — язык
            $table->string('locale', 5)->default('en');

            $table->string('title');
            $table->text('text')->nullable();

            // путь к картинке (hero_1.jpg и т.п.)
            $table->string('image_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
