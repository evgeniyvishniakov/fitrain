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
        // Обновляем все записи с "Блин" на "Отягощения"
        DB::table('exercises')
            ->where('equipment', 'Блин')
            ->update(['equipment' => 'Отягощения']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем обратно "Отягощения" на "Блин"
        DB::table('exercises')
            ->where('equipment', 'Отягощения')
            ->update(['equipment' => 'Блин']);
    }
};
