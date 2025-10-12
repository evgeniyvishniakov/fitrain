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
        Schema::create('system_exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название упражнения
            $table->text('description')->nullable(); // Описание
            $table->string('category'); // Категория (Грудь, Спина, Ноги и т.д.)
            $table->json('muscle_groups'); // Задействованные мышцы
            $table->string('equipment'); // Оборудование (Штанга, Гантели, Собственный вес)
            $table->text('instructions')->nullable(); // Инструкции по выполнению
            $table->string('default_video_url')->nullable(); // Дефолтное видео
            $table->json('fields_config')->nullable(); // Конфигурация полей для записи
            $table->boolean('is_active')->default(true); // Активно ли упражнение
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_exercises');
    }
};















