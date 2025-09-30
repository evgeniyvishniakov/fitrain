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
        Schema::create('user_exercise_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Тренер
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade'); // Системное упражнение
            $table->string('video_url'); // URL видео тренера
            $table->string('title')->nullable(); // Название видео (опционально)
            $table->text('description')->nullable(); // Описание видео (опционально)
            $table->boolean('is_active')->default(true); // Активно ли видео
            $table->timestamps();
            
            // Уникальный индекс - один тренер может иметь только одно видео для упражнения
            $table->unique(['user_id', 'exercise_id']);
            
            // Индексы для быстрого поиска
            $table->index(['exercise_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exercise_videos');
    }
};
