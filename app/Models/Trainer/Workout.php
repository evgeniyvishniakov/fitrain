<?php

namespace App\Models\Trainer;

use App\Models\Crm\BaseModel;
use App\Models\Trainer\Trainer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workout extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'trainer_id',
        'athlete_id',
        'date',
        'time',
        'duration',
        'status',
        'is_counted',
    ];

    protected $casts = [
        'date' => 'date',
        'is_counted' => 'boolean',
    ];
    
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Trainer\Trainer::class, 'trainer_id');
    }
    
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Athlete\Athlete::class, 'athlete_id');
    }
    
    public function progress(): HasMany
    {
        return $this->hasMany(\App\Models\Trainer\Progress::class, 'workout_id');
    }
    
    public function workoutExercises(): HasMany
    {
        return $this->hasMany(\App\Models\Trainer\WorkoutExercise::class, 'workout_id');
    }
    
    public function exercises()
    {
        return $this->belongsToMany(\App\Models\Trainer\Exercise::class, 'workout_exercise', 'workout_id', 'exercise_id')
                    ->withPivot(['sets', 'reps', 'weight', 'rest', 'time', 'distance', 'tempo', 'notes'])
                    ->withTimestamps();
    }
}
