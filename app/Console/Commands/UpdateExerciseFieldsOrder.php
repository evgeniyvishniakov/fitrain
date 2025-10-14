<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trainer\Exercise;

class UpdateExerciseFieldsOrder extends Command
{
    protected $signature = 'exercise:update-fields-order';
    protected $description = 'Update fields_config order for all exercises to weight, reps, sets, rest';

    public function handle()
    {
        $this->info('Updating fields_config order for all exercises...');
        
        // Правильный порядок полей
        $fieldOrder = ['weight', 'reps', 'sets', 'rest', 'time', 'distance', 'tempo'];
        
        $exercises = Exercise::all();
        $updated = 0;
        
        foreach ($exercises as $exercise) {
            if ($exercise->fields_config && is_array($exercise->fields_config)) {
                $oldConfig = $exercise->fields_config;
                
                // Сортируем поля согласно правильному порядку
                $newConfig = $oldConfig;
                usort($newConfig, function($a, $b) use ($fieldOrder) {
                    return array_search($a, $fieldOrder) - array_search($b, $fieldOrder);
                });
                
                // Если порядок изменился - обновляем
                if ($oldConfig !== $newConfig) {
                    $this->line("Updating exercise '{$exercise->name}' (ID: {$exercise->id})");
                    $this->line("  Old order: " . json_encode($oldConfig));
                    $this->line("  New order: " . json_encode($newConfig));
                    
                    $exercise->fields_config = $newConfig;
                    $exercise->save();
                    $updated++;
                }
            }
        }
        
        $this->info("Updated {$updated} exercises with correct field order!");
        
        return 0;
    }
}
