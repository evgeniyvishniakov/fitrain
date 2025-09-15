<?php

namespace App\Models\Crm;

use App\Models\Crm\BaseModel;
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
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }
    
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class, 'workout_id');
    }
}
