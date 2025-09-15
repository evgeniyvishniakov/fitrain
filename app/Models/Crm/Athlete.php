<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Athlete extends User
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'age',
        'weight',
        'height',
        'trainer_id',
    ];
    
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
    
    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class, 'athlete_id');
    }
    
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class, 'athlete_id');
    }
}
