<?php

namespace App\Models\Trainer;

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
        'image_url_2',
        'video_url',
        'is_active',
        'is_system',
        'trainer_id',
        'fields_config'
    ];

    protected $casts = [
        'muscle_groups' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'fields_config' => 'array'
    ];

    // Константы для категорий
    const CATEGORIES = [
        'Грудь' => 'Грудь',
        'Спина' => 'Спина',
        'Ноги' => 'Ноги',
        'Плечи' => 'Плечи',
        'Руки(Бицепс)' => 'Руки(Бицепс)',
        'Руки(Трицепс)' => 'Руки(Трицепс)',
        'Пресс' => 'Пресс',
        'Кардио' => 'Кардио',
        'Гибкость' => 'Гибкость'
    ];

    // Константы для оборудования
    const EQUIPMENT = [
        'Штанга' => 'Штанга',
        'Гантели' => 'Гантели',
        'Собственный вес' => 'Собственный вес',
        'Тренажер' => 'Тренажер',
        'Скакалка' => 'Скакалка',
        'Турник' => 'Турник',
        'Брусья' => 'Брусья',
        'Скамейка' => 'Скамейка'
    ];

    // Константы для сложности
    const DIFFICULTY = [
        'beginner' => 'Начинающий',
        'intermediate' => 'Средний',
        'advanced' => 'Продвинутый'
    ];

    // Константы для полей упражнений
    const AVAILABLE_FIELDS = [
        'sets' => 'Подходы',
        'reps' => 'Повторения',
        'weight' => 'Вес (кг)',
        'rest' => 'Отдых (мин)',
        'time' => 'Время (сек)',
        'distance' => 'Дистанция (м)',
        'tempo' => 'Темп/Скорость'
    ];

    // Скоупы
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeUser($query)
    {
        return $query->where('is_system', false);
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

    // Получить конфигурацию полей для упражнения
    public function getFieldsConfig()
    {
        return $this->fields_config ?? ['sets', 'reps', 'weight', 'rest']; // По умолчанию
    }

    // Получить доступные поля с лейблами
    public function getAvailableFieldsWithLabels()
    {
        $config = $this->getFieldsConfig();
        $fields = [];
        
        foreach ($config as $field) {
            if (isset(self::AVAILABLE_FIELDS[$field])) {
                $fields[$field] = self::AVAILABLE_FIELDS[$field];
            }
        }
        
        return $fields;
    }

    // Связи
    public function trainer()
    {
        return $this->belongsTo(\App\Models\User::class, 'trainer_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'trainer_id');
    }

    public function workouts()
    {
        return $this->belongsToMany(\App\Models\Trainer\Workout::class, 'workout_exercise', 'exercise_id', 'workout_id')
                    ->withPivot(['sets', 'reps', 'weight', 'rest', 'time', 'distance', 'tempo', 'notes'])
                    ->withTimestamps();
    }
}