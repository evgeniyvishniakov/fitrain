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
        Schema::table('workout_exercise', function (Blueprint $table) {
            // Конвертируем существующие данные из секунд в минуты
            DB::statement('UPDATE workout_exercise SET rest = ROUND(rest / 60, 1) WHERE rest IS NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_exercise', function (Blueprint $table) {
            // Конвертируем обратно из минут в секунды
            DB::statement('UPDATE workout_exercise SET rest = ROUND(rest * 60) WHERE rest IS NOT NULL');
        });
    }
};
