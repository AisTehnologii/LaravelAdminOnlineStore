<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type');              // banner|slider|card|project|quote|blog_card
            $table->string('title');             // "Баннер на главной"
            $table->string('slug')->unique();    // "home-hero"
            $table->unsignedInteger('position')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_sections');
    }
};
