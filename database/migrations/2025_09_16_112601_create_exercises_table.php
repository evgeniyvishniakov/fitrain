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
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // Грудь, Спина, Ноги, Плечи, Руки, Кардио
            $table->string('equipment'); // Штанга, Гантели, Собственный вес, Тренажеры
            $table->json('muscle_groups')->nullable(); // Основные и дополнительные группы мышц
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->text('instructions')->nullable(); // Пошаговые инструкции
            $table->string('image_url')->nullable(); // URL изображения упражнения
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
