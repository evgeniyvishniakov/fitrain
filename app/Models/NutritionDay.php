<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'nutrition_plan_id',
        'date',
        'proteins',
        'fats', 
        'carbs',
        'calories',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'proteins' => 'decimal:2',
        'fats' => 'decimal:2',
        'carbs' => 'decimal:2',
        'calories' => 'decimal:2',
    ];

    /**
     * Связь с планом питания
     */
    public function nutritionPlan(): BelongsTo
    {
        return $this->belongsTo(NutritionPlan::class);
    }

    /**
     * Автоматический расчет калорий при сохранении
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Калории = (белки × 4) + (жиры × 9) + (углеводы × 4)
            $model->calories = ($model->proteins * 4) + ($model->fats * 9) + ($model->carbs * 4);
        });
    }

    /**
     * Получить процент белка от общих калорий
     */
    public function getProteinPercentageAttribute(): float
    {
        if ($this->calories == 0) return 0;
        return round(($this->proteins * 4 / $this->calories) * 100, 1);
    }

    /**
     * Получить процент жиров от общих калорий
     */
    public function getFatPercentageAttribute(): float
    {
        if ($this->calories == 0) return 0;
        return round(($this->fats * 9 / $this->calories) * 100, 1);
    }

    /**
     * Получить процент углеводов от общих калорий
     */
    public function getCarbPercentageAttribute(): float
    {
        if ($this->calories == 0) return 0;
        return round(($this->carbs * 4 / $this->calories) * 100, 1);
    }
}
