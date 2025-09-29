<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NutritionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'trainer_id', 
        'month',
        'year',
        'title',
        'description'
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Связь с пользователем (спортсмен)
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(User::class, 'athlete_id');
    }

    /**
     * Связь с пользователем (тренер)
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * Связь с днями питания
     */
    public function nutritionDays(): HasMany
    {
        return $this->hasMany(NutritionDay::class)->orderBy('date');
    }

    /**
     * Получить название месяца
     */
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
            5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
            9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];
        
        return $months[$this->month] ?? 'Неизвестно';
    }

    /**
     * Получить полное название плана
     */
    public function getFullTitleAttribute(): string
    {
        return $this->title ?: "План питания на {$this->month_name} {$this->year}";
    }
}
