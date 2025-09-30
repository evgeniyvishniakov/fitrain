<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Переносим данные из system_exercises в exercises
        $systemExercises = DB::table('system_exercises')->get();
        
        foreach ($systemExercises as $systemExercise) {
            DB::table('exercises')->insert([
                'name' => $systemExercise->name,
                'description' => $systemExercise->description,
                'category' => $systemExercise->category,
                'equipment' => $systemExercise->equipment,
                'muscle_groups' => $systemExercise->muscle_groups,
                'instructions' => $systemExercise->instructions,
                'video_url' => $systemExercise->default_video_url,
                'fields_config' => $systemExercise->fields_config,
                'is_active' => $systemExercise->is_active,
                'is_system' => true, // Помечаем как системное
                'created_at' => $systemExercise->created_at,
                'updated_at' => $systemExercise->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем системные упражнения, которые были перенесены
        DB::table('exercises')->where('is_system', true)->delete();
    }
};
