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
        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('month'); // 1-12
            $table->smallInteger('year'); // 2025
            $table->string('title')->nullable(); // "План питания на январь"
            $table->text('description')->nullable(); // Комментарии тренера
            $table->timestamps();
            
            // Уникальность: один план на месяц для спортсмена
            $table->unique(['athlete_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_plans');
    }
};
