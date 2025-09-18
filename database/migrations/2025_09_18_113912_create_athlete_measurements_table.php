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
        Schema::create('athlete_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained('users')->onDelete('cascade');
            $table->date('measurement_date');
            
            // Физические параметры
            $table->decimal('weight', 5, 2)->nullable(); // вес в кг
            $table->decimal('height', 5, 2)->nullable(); // рост в см
            $table->decimal('body_fat_percentage', 5, 2)->nullable(); // процент жира
            $table->decimal('muscle_mass', 5, 2)->nullable(); // мышечная масса в кг
            $table->decimal('water_percentage', 5, 2)->nullable(); // процент воды
            
            // Объемы тела
            $table->decimal('chest', 5, 2)->nullable(); // грудь в см
            $table->decimal('waist', 5, 2)->nullable(); // талия в см
            $table->decimal('hips', 5, 2)->nullable(); // бедра в см
            $table->decimal('bicep', 5, 2)->nullable(); // бицепс в см
            $table->decimal('thigh', 5, 2)->nullable(); // бедро в см
            $table->decimal('neck', 5, 2)->nullable(); // шея в см
            
            // Дополнительные измерения
            $table->decimal('resting_heart_rate', 5, 2)->nullable(); // пульс в покое
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable(); // систолическое давление
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable(); // диастолическое давление
            
            // Метаданные
            $table->text('notes')->nullable(); // заметки
            $table->json('photos')->nullable(); // фото до/после
            $table->string('measured_by')->nullable(); // кто измерял (тренер/сам спортсмен)
            
            $table->timestamps();
            
            // Индексы
            $table->index(['athlete_id', 'measurement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athlete_measurements');
    }
};
