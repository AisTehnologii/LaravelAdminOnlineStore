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
        Schema::create('onec_groups', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->id();
    $table->string('external_id')->unique();
    $table->string('parent_external_id')->nullable()->index();
    $table->string('name');
    $table->string('locale', 8)->default('ru')->index();
    $table->unsignedInteger('position')->default(0);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onec_groups');
    }
};
