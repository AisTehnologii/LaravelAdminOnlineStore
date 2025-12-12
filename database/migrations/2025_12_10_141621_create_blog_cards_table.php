<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_cards', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5)->default('en');
            $table->date('date')->nullable();
            $table->string('title');
            $table->string('url')->nullable();      // ссылка "Read more"
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_cards');
    }
};

