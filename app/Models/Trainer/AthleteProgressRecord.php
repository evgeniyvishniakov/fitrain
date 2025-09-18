<?php

namespace App\Models\Trainer;

use App\Models\Shared\User;
use App\Models\Trainer\Workout;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AthleteProgressRecord extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'athlete_id',
        'workout_id',
        'record_date',
        'category',
        'exercise_name',
        'weight',
        'reps',
        'sets',
        'max_weight',
        'exercise_type',
        'duration_minutes',
        'distance',
        'speed',
        'heart_rate_avg',
        'heart_rate_max',
        'flexibility_test',
        'flexibility_score',
        'value',
        'unit',
        'notes',
        'additional_data',
        'recorded_by',
        'is_personal_record',
        'photos',
    ];
    
    protected $casts = [
        'record_date' => 'date',
        'weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'distance' => 'decimal:2',
        'speed' => 'decimal:2',
        'flexibility_score' => 'decimal:2',
        'value' => 'decimal:2',
        'additional_data' => 'array',
        'is_personal_record' => 'boolean',
        'photos' => 'array',
    ];
    
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(User::class, 'athlete_id');
    }
    
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class, 'workout_id');
    }
    
    // Константы для категорий
    const CATEGORY_STRENGTH = 'strength';
    const CATEGORY_ENDURANCE = 'endurance';
    const CATEGORY_FLEXIBILITY = 'flexibility';
    const CATEGORY_WEIGHT = 'weight';
    const CATEGORY_MEASUREMENTS = 'measurements';
    
    public static function getCategories()
    {
        return [
            self::CATEGORY_STRENGTH => 'Силовые показатели',
            self::CATEGORY_ENDURANCE => 'Выносливость',
            self::CATEGORY_FLEXIBILITY => 'Гибкость',
            self::CATEGORY_WEIGHT => 'Вес',
            self::CATEGORY_MEASUREMENTS => 'Измерения',
        ];
    }
}
