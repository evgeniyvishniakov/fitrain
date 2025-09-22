<?php

namespace App\Models\Athlete;

use App\Models\Crm\BaseModel;
use App\Models\Trainer\Workout;
use App\Models\Trainer\Exercise;
use App\Models\Shared\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseProgress extends BaseModel
{
    protected $table = 'workout_exercise_progress';
    
    protected $fillable = [
        'workout_id',
        'exercise_id', 
        'athlete_id',
        'status',
        'athlete_comment',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(User::class, 'athlete_id');
    }
}
