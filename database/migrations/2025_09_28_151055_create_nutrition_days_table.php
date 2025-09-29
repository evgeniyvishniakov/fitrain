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
        Schema::create('nutrition_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nutrition_plan_id')->constrained('nutrition_plans')->onDelete('cascade');
            $table->date('date'); // 2025-01-15
            $table->decimal('proteins', 6, 2)->default(0); // граммы белка
            $table->decimal('fats', 6, 2)->default(0); // граммы жиров
            $table->decimal('carbs', 6, 2)->default(0); // граммы углеводов
            $table->decimal('calories', 8, 2)->default(0); // калории (авторасчет)
            $table->text('notes')->nullable(); // комментарии тренера
            $table->timestamps();
            
            // Уникальность: один день на план
            $table->unique(['nutrition_plan_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_days');
    }
};
