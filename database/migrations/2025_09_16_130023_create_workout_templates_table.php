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
        Schema::create('workout_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название шаблона
            $table->text('description')->nullable(); // Описание шаблона
            $table->enum('category', ['strength', 'cardio', 'flexibility', 'mixed']); // Тип тренировки
            $table->integer('estimated_duration')->nullable(); // Продолжительность в минутах
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner'); // Сложность
            $table->json('exercises')->nullable(); // JSON с упражнениями и их параметрами
            $table->foreignId('created_by')->constrained('users'); // Кто создал шаблон
            $table->boolean('is_public')->default(false); // Публичный ли шаблон
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_templates');
    }
};