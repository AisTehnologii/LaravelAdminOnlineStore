<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();

            $table->string('section');            // layout.header, home.about_card...
            $table->string('key');                // menu_left_home, title, text_1...
            $table->longText('value')->nullable();
            $table->string('locale', 5)->default('en'); // en/ru/ro

            $table->timestamps();

            $table->index(['section', 'locale']);
            $table->unique(['section', 'key', 'locale']); // чтобы не было дублей
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
