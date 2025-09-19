<?php

namespace App\Models\Trainer;

use App\Models\Crm\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutExercise extends BaseModel
{
    protected $fillable = [
        'workout_id',
        'exercise_id',
        'sets',
        'reps',
        'weight',
        'rest',
        'time',
        'distance',
        'tempo',
        'notes',
    ];

    protected $casts = [
        'sets' => 'integer',
        'reps' => 'integer',
        'weight' => 'decimal:2',
        'rest' => 'integer',
        'time' => 'integer',
        'distance' => 'integer',
    ];

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}