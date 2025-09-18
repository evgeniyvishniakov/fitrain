<?php

namespace App\Models\Trainer;

use App\Models\Shared\User;
use App\Models\Trainer\Athlete;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trainer extends User
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'specialization',
        'experience_years',
    ];
    
    public function athletes(): HasMany
    {
        return $this->hasMany(\App\Models\Trainer\Athlete::class, 'trainer_id');
    }
    
    public function workouts(): HasMany
    {
        return $this->hasMany(\App\Models\Trainer\Workout::class, 'trainer_id');
    }
}
