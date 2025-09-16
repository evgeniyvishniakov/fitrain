<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'equipment',
        'muscle_groups',
        'difficulty',
        'instructions',
        'image_url',
        'is_active'
    ];

    protected $casts = [
        'muscle_groups' => 'array',
        'is_active' => 'boolean'
    ];

    // Константы для категорий
    const CATEGORIES = [
        'chest' => 'Грудь',
        'back' => 'Спина',
        'legs' => 'Ноги',
        'shoulders' => 'Плечи',
        'arms' => 'Руки',
        'cardio' => 'Кардио',
        'core' => 'Пресс'
    ];

    // Константы для оборудования
    const EQUIPMENT = [
        'barbell' => 'Штанга',
        'dumbbell' => 'Гантели',
        'bodyweight' => 'Собственный вес',
        'machine' => 'Тренажер',
        'cable' => 'Тросы',
        'kettlebell' => 'Гири'
    ];

    // Константы для сложности
    const DIFFICULTY = [
        'beginner' => 'Начинающий',
        'intermediate' => 'Средний',
        'advanced' => 'Продвинутый'
    ];

    // Скоупы
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByEquipment($query, $equipment)
    {
        return $query->where('equipment', $equipment);
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

    public function getEquipmentLabelAttribute()
    {
        return self::EQUIPMENT[$this->equipment] ?? $this->equipment;
    }

    public function getDifficultyLabelAttribute()
    {
        return self::DIFFICULTY[$this->difficulty] ?? $this->difficulty;
    }
}
