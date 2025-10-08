<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateWorkoutExerciseOrder extends Command
{
    protected $signature = 'workout:update-exercise-order';
    protected $description = 'Update order_index for existing workout exercises';

    public function handle()
    {
        $this->info('Updating order_index for existing workout exercises...');
        
        // Получаем все тренировки
        $workouts = DB::table('workouts')->get();
        
        foreach ($workouts as $workout) {
            // Получаем все упражнения для каждой тренировки, отсортированные по id
            $exercises = DB::table('workout_exercise')
                ->where('workout_id', $workout->id)
                ->orderBy('id', 'asc')
                ->get();
            
            // Обновляем order_index для каждого упражнения
            foreach ($exercises as $index => $exercise) {
                DB::table('workout_exercise')
                    ->where('id', $exercise->id)
                    ->update(['order_index' => $index]);
            }
        }
        
        $this->info('Order index updated successfully!');
        
        return 0;
    }
}
