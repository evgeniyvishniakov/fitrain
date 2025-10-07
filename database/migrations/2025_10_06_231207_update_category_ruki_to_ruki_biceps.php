<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Обновляем все записи с "Руки" на "Руки(Бицепс)"
        DB::table('exercises')
            ->where('category', 'Руки')
            ->update(['category' => 'Руки(Бицепс)']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем обратно "Руки(Бицепс)" на "Руки"
        DB::table('exercises')
            ->where('category', 'Руки(Бицепс)')
            ->update(['category' => 'Руки']);
    }
};
