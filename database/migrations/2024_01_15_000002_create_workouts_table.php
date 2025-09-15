<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('trainer_id');
            $table->unsignedBigInteger('athlete_id');
            $table->date('date');
            $table->integer('duration')->nullable(); // в минутах
            $table->enum('status', ['planned', 'completed', 'cancelled'])->default('planned');
            $table->timestamps();

            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('athlete_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
