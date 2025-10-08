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
        
        $exercises = Exercise::all();
        $updated = 0;
        
        foreach ($exercises as $exercise) {
            if ($exercise->fields_config) {
                $config = is_array($exercise->fields_config) ? $exercise->fields_config : json_decode($exercise->fields_config, true);
                
                if ($config === ['sets', 'reps', 'weight', 'rest']) {
                    $exercise->fields_config = ['weight', 'reps', 'sets', 'rest'];
                    $exercise->save();
                    $updated++;
                }
            }
        }
        
        $this->info("Updated {$updated} exercises with new field order!");
        
        return 0;
    }
}
