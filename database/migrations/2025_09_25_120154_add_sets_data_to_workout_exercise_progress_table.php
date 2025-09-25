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
        Schema::table('workout_exercise_progress', function (Blueprint $table) {
            // Добавляем поле для хранения данных по подходам в JSON формате
            $table->json('sets_data')->nullable()->after('athlete_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_exercise_progress', function (Blueprint $table) {
            $table->dropColumn('sets_data');
        });
    }
};
