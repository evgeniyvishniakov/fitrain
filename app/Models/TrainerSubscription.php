<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'subscription_plan_id',
        'status',
        'price',
        'currency_code',
        'start_date',
        'expires_date',
        'is_trial',
        'trial_days',
        'notes'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
        'expires_date' => 'date',
        'is_trial' => 'boolean',
        'trial_days' => 'integer'
    ];

    /**
     * Отношение к тренеру
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * Отношение к плану подписки
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Отношение к валюте
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * Скоупы
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')->orWhere(function($q) {
            $q->where('status', 'trial')
              ->where('expires_date', '<', now());
        });
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('expires_date', '<=', now()->addDays($days))
                     ->where('expires_date', '>', now());
    }
}
