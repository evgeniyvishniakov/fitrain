<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'category', 'estimated_duration', 'difficulty',
        'exercises', 'created_by', 'is_public', 'is_active'
    ];

    protected $casts = [
        'exercises' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean'
    ];

    const CATEGORIES = [
        'strength' => 'Силовая',
        'cardio' => 'Кардио',
        'flexibility' => 'Гибкость',
        'mixed' => 'Смешанная'
    ];

    const DIFFICULTY_LEVELS = [
        'beginner' => 'Начинающий',
        'intermediate' => 'Средний',
        'advanced' => 'Продвинутый'
    ];

    // Отношения
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Скоупы
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    // Аксессоры
    public function getCategoryLabelAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getDifficultyLabelAttribute()
    {
        return self::DIFFICULTY_LEVELS[$this->difficulty] ?? $this->difficulty;
    }

    public function getExercisesCountAttribute()
    {
        return is_array($this->exercises) ? count($this->exercises) : 0;
    }
}