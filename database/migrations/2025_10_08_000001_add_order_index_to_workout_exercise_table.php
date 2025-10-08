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
            // Проверяем, существует ли колонка order_index
            if (!Schema::hasColumn('workout_exercise', 'order_index')) {
                $table->integer('order_index')->default(0)->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_exercise', function (Blueprint $table) {
            if (Schema::hasColumn('workout_exercise', 'order_index')) {
                $table->dropColumn('order_index');
            }
        });
    }
};
