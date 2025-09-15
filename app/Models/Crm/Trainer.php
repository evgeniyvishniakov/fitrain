<?php

namespace App\Models\Crm;

use App\Models\User;
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
        return $this->hasMany(Athlete::class, 'trainer_id');
    }
    
    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class, 'trainer_id');
    }
}
