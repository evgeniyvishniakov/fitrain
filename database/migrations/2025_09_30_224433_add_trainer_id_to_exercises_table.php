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
        Schema::table('exercises', function (Blueprint $table) {
            $table->unsignedBigInteger('trainer_id')->nullable()->after('is_system');
            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['trainer_id', 'is_system']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropIndex(['trainer_id', 'is_system']);
            $table->dropColumn('trainer_id');
        });
    }
};
