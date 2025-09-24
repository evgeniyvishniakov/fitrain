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
        'package_type',
        'total_sessions',
        'used_sessions',
        'package_price',
        'purchase_date',
        'expires_date',
        'payment_method',
        'payment_description',
        'payment_history',
        'total_paid',
        'last_payment_date',
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
        'purchase_date' => 'date',
        'expires_date' => 'date',
        'last_payment_date' => 'date',
        'payment_history' => 'array',
        'package_price' => 'decimal:2',
        'total_paid' => 'decimal:2',
    ];

    /**
     * Отношения для тренера
     */
    public function athletes()
    {
        return $this->hasMany(\App\Models\Shared\User::class, 'trainer_id');
    }

    public function trainerWorkouts()
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

    /**
     * Тренировки спортсмена
     */
    public function workouts()
    {
        return $this->hasMany(\App\Models\Trainer\Workout::class, 'athlete_id');
    }

    /**
     * Измерения спортсмена
     */
    public function measurements()
    {
        return $this->hasMany(\App\Models\Trainer\AthleteMeasurement::class, 'athlete_id');
    }

}
