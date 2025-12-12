<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            // если колонки ещё нет — добавляем
            if (! Schema::hasColumn('content_blocks', 'section')) {
                $table->string('section')
                    ->default('home.general')
                    ->index()
                    ->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            if (Schema::hasColumn('content_blocks', 'section')) {
                $table->dropColumn('section');
            }
        });
    }
};

