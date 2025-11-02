<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency_code',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Отношение к валюте
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * Отношение к подпискам тренеров
     */
    public function trainerSubscriptions()
    {
        return $this->hasMany(TrainerSubscription::class);
    }

    /**
     * Скоупы
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
