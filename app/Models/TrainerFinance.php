<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'athlete_id',
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

    protected $casts = [
        'payment_history' => 'array',
        'package_price' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'purchase_date' => 'date',
        'expires_date' => 'date',
        'last_payment_date' => 'date',
    ];

    /**
     * Отношение к тренеру
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * Отношение к спортсмену
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(User::class, 'athlete_id');
    }
}
