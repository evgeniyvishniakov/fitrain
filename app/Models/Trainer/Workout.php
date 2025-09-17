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
        'duration',
        'status',
    ];
    
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
    
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Athlete\Athlete::class, 'athlete_id');
    }
    
    public function progress(): HasMany
    {
        return $this->hasMany(\App\Models\Athlete\Progress::class, 'workout_id');
    }
}
