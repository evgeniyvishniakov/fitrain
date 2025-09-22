<?php

namespace App\Models\Trainer;

use App\Models\Shared\User;
use App\Models\Athlete\Athlete;
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
        return $this->hasMany(\App\Models\Athlete\Athlete::class, 'trainer_id');
    }
    
    public function trainerWorkouts(): HasMany
    {
        return $this->hasMany(\App\Models\Trainer\Workout::class, 'trainer_id');
    }
    
    // Переопределяем метод workouts для тренеров
    public function workouts(): HasMany
    {
        if ($this->hasRole('trainer')) {
            return $this->hasMany(\App\Models\Trainer\Workout::class, 'trainer_id');
        } else {
            return $this->hasMany(\App\Models\Trainer\Workout::class, 'athlete_id');
        }
    }
}
