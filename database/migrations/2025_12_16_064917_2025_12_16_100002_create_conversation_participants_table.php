<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('last_read_at')->nullable();
            $table->enum('role', ['owner', 'member'])->nullable();

            $table->timestamps();

            $table->unique(['conversation_id', 'user_id'], 'cp_unique_conv_user');
            $table->index(['user_id', 'conversation_id'], 'cp_user_conv_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};
