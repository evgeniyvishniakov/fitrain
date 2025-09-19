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
        Schema::table('workout_exercise', function (Blueprint $table) {
            $table->integer('time')->nullable()->default(0)->after('rest');
            $table->integer('distance')->nullable()->default(0)->after('time');
            $table->string('tempo')->nullable()->after('distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_exercise', function (Blueprint $table) {
            $table->dropColumn(['time', 'distance', 'tempo']);
        });
    }
};