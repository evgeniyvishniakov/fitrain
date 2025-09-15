<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('specialization')->nullable();
            $table->integer('experience_years')->nullable();
            $table->integer('age')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->string('role')->default('athlete'); // trainer, athlete
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'specialization', 
                'experience_years',
                'age',
                'weight',
                'height',
                'trainer_id',
                'role'
            ]);
        });
    }
};
