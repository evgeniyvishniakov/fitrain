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
        Schema::create('athlete_progress_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('workout_id')->nullable()->constrained('workouts')->onDelete('set null');
            $table->date('record_date');
            $table->string('category'); // 'strength', 'endurance', 'flexibility', 'weight', 'measurements'
            
            // Силовые показатели
            $table->string('exercise_name')->nullable(); // название упражнения
            $table->decimal('weight', 8, 2)->nullable(); // вес в кг
            $table->integer('reps')->nullable(); // количество повторений
            $table->integer('sets')->nullable(); // количество подходов
            $table->decimal('max_weight', 8, 2)->nullable(); // максимальный вес (личный рекорд)
            
            // Выносливость
            $table->string('exercise_type')->nullable(); // тип упражнения (бег, велосипед и т.д.)
            $table->integer('duration_minutes')->nullable(); // продолжительность в минутах
            $table->decimal('distance', 8, 2)->nullable(); // дистанция в км
            $table->decimal('speed', 5, 2)->nullable(); // скорость км/ч
            $table->integer('heart_rate_avg')->nullable(); // средний пульс
            $table->integer('heart_rate_max')->nullable(); // максимальный пульс
            
            // Гибкость
            $table->string('flexibility_test')->nullable(); // тест на гибкость
            $table->decimal('flexibility_score', 5, 2)->nullable(); // результат в см/градусах
            
            // Общие показатели
            $table->decimal('value', 10, 2)->nullable(); // общее значение (для любых метрик)
            $table->string('unit')->nullable(); // единица измерения
            $table->text('notes')->nullable(); // заметки
            $table->json('additional_data')->nullable(); // дополнительные данные в JSON
            
            // Метаданные
            $table->string('recorded_by')->nullable(); // кто записал (тренер/спортсмен)
            $table->boolean('is_personal_record')->default(false); // личный рекорд
            $table->json('photos')->nullable(); // фото/видео записи
            
            $table->timestamps();
            
            // Индексы
            $table->index(['athlete_id', 'record_date']);
            $table->index(['athlete_id', 'category']);
            $table->index(['exercise_name', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athlete_progress_records');
    }
};
