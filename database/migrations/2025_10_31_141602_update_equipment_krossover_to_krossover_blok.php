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
        // Обновляем все записи с "Кроссовер" на "Кроссовер / Блок"
        DB::table('exercises')
            ->where('equipment', 'Кроссовер')
            ->update(['equipment' => 'Кроссовер / Блок']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем обратно "Кроссовер / Блок" на "Кроссовер"
        DB::table('exercises')
            ->where('equipment', 'Кроссовер / Блок')
            ->update(['equipment' => 'Кроссовер']);
    }
};
