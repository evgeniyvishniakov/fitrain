<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('athlete_id');
            $table->date('date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack']);
            $table->string('food_name');
            $table->integer('calories')->nullable();
            $table->decimal('protein', 5, 2)->nullable();
            $table->decimal('carbs', 5, 2)->nullable();
            $table->decimal('fat', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('athlete_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition');
    }
};
