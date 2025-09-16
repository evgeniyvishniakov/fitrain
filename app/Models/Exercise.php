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
        'Грудь' => 'Грудь',
        'Спина' => 'Спина',
        'Ноги' => 'Ноги',
        'Плечи' => 'Плечи',
        'Руки' => 'Руки',
        'Кардио' => 'Кардио',
        'Гибкость' => 'Гибкость'
    ];

    // Константы для оборудования
    const EQUIPMENT = [
        'Штанга' => 'Штанга',
        'Гантели' => 'Гантели',
        'Собственный вес' => 'Собственный вес',
        'Тренажеры' => 'Тренажеры',
        'Скакалка' => 'Скакалка',
        'Турник' => 'Турник'
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
