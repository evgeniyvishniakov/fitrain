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
        // Обновляем все записи с "Тренажеры" на "Тренажер"
        DB::table('exercises')
            ->where('equipment', 'Тренажеры')
            ->update(['equipment' => 'Тренажер']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем обратно "Тренажер" на "Тренажеры"
        DB::table('exercises')
            ->where('equipment', 'Тренажер')
            ->update(['equipment' => 'Тренажеры']);
    }
};
