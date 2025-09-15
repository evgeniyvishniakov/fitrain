<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('athlete_id');
            $table->unsignedBigInteger('workout_id');
            $table->date('date');
            $table->decimal('weight', 5, 2)->nullable();
            $table->integer('reps')->nullable();
            $table->integer('sets')->nullable();
            $table->text('notes')->nullable();
            $table->json('photos')->nullable(); // массив ссылок на фото
            $table->timestamps();

            $table->foreign('athlete_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workout_id')->references('id')->on('workouts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
