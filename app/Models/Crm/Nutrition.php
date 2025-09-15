<?php

namespace App\Models\Crm;

use App\Models\Crm\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nutrition extends BaseModel
{
    protected $fillable = [
        'athlete_id',
        'date',
        'meal_type',
        'food_name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'notes',
    ];
    
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class, 'athlete_id');
    }
}
