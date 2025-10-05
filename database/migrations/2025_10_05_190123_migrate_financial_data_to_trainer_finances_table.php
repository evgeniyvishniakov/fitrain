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
        // Переносим финансовые данные из таблицы users в trainer_finances
        $athletes = DB::table('users')
            ->whereNotNull('trainer_id')
            ->whereNotNull('package_type')
            ->get();

        foreach ($athletes as $athlete) {
            // Проверяем, есть ли уже запись для этого спортсмена
            $existing = DB::table('trainer_finances')
                ->where('trainer_id', $athlete->trainer_id)
                ->where('athlete_id', $athlete->id)
                ->first();

            if (!$existing) {
                DB::table('trainer_finances')->insert([
                    'trainer_id' => $athlete->trainer_id,
                    'athlete_id' => $athlete->id,
                    'package_type' => $athlete->package_type,
                    'total_sessions' => $athlete->total_sessions ?? 0,
                    'used_sessions' => $athlete->used_sessions ?? 0,
                    'package_price' => $athlete->package_price ?? 0,
                    'purchase_date' => $athlete->purchase_date,
                    'expires_date' => $athlete->expires_date,
                    'payment_method' => $athlete->payment_method,
                    'payment_description' => $athlete->payment_description,
                    'payment_history' => $athlete->payment_history,
                    'total_paid' => $athlete->total_paid ?? 0,
                    'last_payment_date' => $athlete->last_payment_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Очищаем таблицу trainer_finances
        DB::table('trainer_finances')->truncate();
    }
};
