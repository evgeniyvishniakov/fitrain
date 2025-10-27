<?php

namespace App\Models\Trainer;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FavoriteExercise extends Model
{
    protected $fillable = [
        'user_id',
        'exercise_id',
    ];

    /**
     * Пользователь (тренер)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Упражнение
     */
    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}

