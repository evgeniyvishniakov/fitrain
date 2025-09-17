<?php

namespace App\Models\Athlete;

use App\Models\Shared\User;
use App\Models\Trainer\Trainer;
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
        return $this->hasMany(\App\Models\Trainer\Workout::class, 'athlete_id');
    }
    
    public function progress(): HasMany
    {
        return $this->hasMany(\App\Models\Athlete\Progress::class, 'athlete_id');
    }
}
