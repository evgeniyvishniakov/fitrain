<?php

namespace App\Models\Trainer;

use App\Models\Crm\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progress extends BaseModel
{
    protected $fillable = [
        'athlete_id',
        'workout_id',
        'date',
        'weight',
        'reps',
        'sets',
        'notes',
        'photos',
    ];
    
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Athlete\Athlete::class, 'athlete_id');
    }
    
    public function workout(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Trainer\Workout::class, 'workout_id');
    }
}
