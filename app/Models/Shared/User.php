<?php

namespace App\Models\Shared;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'specialization',
        'experience_years',
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_medical_checkup' => 'date',
        'goals' => 'array',
        'contact_info' => 'array',
        'health_restrictions' => 'array',
        'medical_documents' => 'array',
        'profile_modules' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Отношения для тренера
     */
    public function athletes()
    {
        return $this->hasMany(\App\Models\Shared\User::class, 'trainer_id');
    }

    public function workouts()
    {
        return $this->hasMany(\App\Models\Trainer\Workout::class, 'trainer_id');
    }

    /**
     * Отношения для спортсмена
     */
    public function trainer()
    {
        return $this->belongsTo(\App\Models\Shared\User::class, 'trainer_id');
    }

    public function athleteWorkouts()
    {
        return $this->hasMany(\App\Models\Trainer\Workout::class, 'athlete_id');
    }

    public function progress()
    {
        return $this->hasMany(\App\Models\Trainer\Progress::class, 'athlete_id');
    }

    public function nutrition()
    {
        return $this->hasMany(\App\Models\Trainer\Nutrition::class, 'athlete_id');
    }

    /**
     * Проверка роли
     */
    public function isTrainer()
    {
        return $this->hasRole('trainer');
    }

    public function isAthlete()
    {
        return $this->hasRole('athlete');
    }
}
