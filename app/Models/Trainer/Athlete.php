<?php

namespace App\Models\Trainer;

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
        'avatar',
        'birth_date',
        'gender',
        'sport_level',
        'goals',
        'contact_info',
        'current_weight',
        'current_height',
        'health_restrictions',
        'medical_documents',
        'last_medical_checkup',
        'profile_modules',
        'is_active',
    ];
    
    protected $casts = [
        'goals' => 'array',
        'contact_info' => 'array',
        'health_restrictions' => 'array',
        'medical_documents' => 'array',
        'profile_modules' => 'array',
        'birth_date' => 'date',
        'last_medical_checkup' => 'date',
        'is_active' => 'boolean',
        'current_weight' => 'decimal:2',
        'current_height' => 'decimal:2',
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
        return $this->hasMany(\App\Models\Trainer\Progress::class, 'athlete_id');
    }
    
    public function measurements(): HasMany
    {
        return $this->hasMany(\App\Models\Trainer\AthleteMeasurement::class, 'athlete_id');
    }
    
    public function progressRecords(): HasMany
    {
        return $this->hasMany(\App\Models\Trainer\AthleteProgressRecord::class, 'athlete_id');
    }
    
    public function nutrition(): HasMany
    {
        return $this->hasMany(\App\Models\Trainer\Nutrition::class, 'athlete_id');
    }
    
    // Геттеры для вычисляемых полей
    public function getBmiAttribute()
    {
        if ($this->current_weight && $this->current_height) {
            return round($this->current_weight / (($this->current_height/100) ** 2), 1);
        }
        return null;
    }
    
    public function getAgeAttribute()
    {
        if ($this->birth_date) {
            return $this->birth_date->age;
        }
        return null;
    }
    
    public function getLatestMeasurementAttribute()
    {
        return $this->measurements()->latest('measurement_date')->first();
    }
    
    public function getLatestProgressRecordAttribute()
    {
        return $this->progressRecords()->latest('record_date')->first();
    }
}
