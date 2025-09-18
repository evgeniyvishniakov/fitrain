<?php

namespace App\Models\Trainer;

use App\Models\Shared\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AthleteMeasurement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'athlete_id',
        'measurement_date',
        'weight',
        'height',
        'body_fat_percentage',
        'muscle_mass',
        'water_percentage',
        'chest',
        'waist',
        'hips',
        'bicep',
        'thigh',
        'neck',
        'resting_heart_rate',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'notes',
        'photos',
        'measured_by',
    ];
    
    protected $casts = [
        'measurement_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'body_fat_percentage' => 'decimal:2',
        'muscle_mass' => 'decimal:2',
        'water_percentage' => 'decimal:2',
        'chest' => 'decimal:2',
        'waist' => 'decimal:2',
        'hips' => 'decimal:2',
        'bicep' => 'decimal:2',
        'thigh' => 'decimal:2',
        'neck' => 'decimal:2',
        'resting_heart_rate' => 'decimal:2',
        'blood_pressure_systolic' => 'decimal:2',
        'blood_pressure_diastolic' => 'decimal:2',
        'photos' => 'array',
    ];
    
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(User::class, 'athlete_id');
    }
    
    // Геттер для ИМТ
    public function getBmiAttribute()
    {
        if ($this->weight && $this->height) {
            return round($this->weight / (($this->height/100) ** 2), 1);
        }
        return null;
    }
}
