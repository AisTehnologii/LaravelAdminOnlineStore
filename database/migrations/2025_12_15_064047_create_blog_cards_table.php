<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_cards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('content_sections')
                ->nullOnDelete();

            // как раньше
            $table->string('locale', 5)->default('en');
            $table->date('date')->nullable();
            $table->string('title');
            $table->string('url')->nullable();
            $table->integer('position')->default(0);

            $table->timestamps();

            $table->index(['section_id', 'locale', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_cards');
    }
};
