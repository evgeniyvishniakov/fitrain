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
        'fields_config',
        'translations'
    ];

    protected $casts = [
        'muscle_groups' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'fields_config' => 'array',
        'translations' => 'array'
    ];

    // Константы для категорий
    const CATEGORIES = [
        'Грудь' => 'Грудь',
        'Спина' => 'Спина',
        'Ноги(Бедра)' => 'Ноги(Бедра)',
        'Ноги(Икры)' => 'Ноги(Икры)',
        'Плечи' => 'Плечи',
        'Руки(Бицепс)' => 'Руки(Бицепс)',
        'Руки(Трицепс)' => 'Руки(Трицепс)',
        'Руки(Предплечье)' => 'Руки(Предплечье)',
        'Пресс' => 'Пресс',
        'Кардио' => 'Кардио',
        'Гибкость' => 'Гибкость'
    ];

    // Константы для оборудования
    const EQUIPMENT = [
        'Штанга' => 'Штанга',
        'Гриф' => 'Гриф',
        'Трап-гриф' => 'Трап-гриф',
        'EZ-гриф' => 'EZ-гриф',
        'Блин' => 'Блин',
        'Гантели' => 'Гантели',
        'Собственный вес' => 'Собственный вес',
        'Тренажер' => 'Тренажер',
        'Машина Смита' => 'Машина Смита',
        'Кроссовер' => 'Кроссовер',
        'Скакалка' => 'Скакалка',
        'Турник' => 'Турник',
        'Брусья' => 'Брусья',
        'Скамейка' => 'Скамейка',
        'Резина / Экспандер' => 'Резина / Экспандер'
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

    /**
     * Пользователи, которые добавили это упражнение в избранное
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(\App\Models\User::class, 'favorite_exercises', 'exercise_id', 'user_id')
                    ->withTimestamps();
    }

    // Методы для работы с избранным
    
    /**
     * Проверить, находится ли упражнение в избранном у пользователя
     */
    public function isFavoritedBy($userId)
    {
        return $this->favoritedBy()->where('user_id', $userId)->exists();
    }

    /**
     * Добавить в избранное
     */
    public function addToFavorites($userId)
    {
        if (!$this->isFavoritedBy($userId)) {
            FavoriteExercise::create([
                'user_id' => $userId,
                'exercise_id' => $this->id,
            ]);
        }
    }

    /**
     * Удалить из избранного
     */
    public function removeFromFavorites($userId)
    {
        FavoriteExercise::where('user_id', $userId)
            ->where('exercise_id', $this->id)
            ->delete();
    }

    /**
     * Scope для избранных упражнений пользователя
     */
    public function scopeFavorites($query, $userId)
    {
        return $query->whereHas('favoritedBy', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // Методы для работы с переводами
    
    /**
     * Переопределяем метод getAttribute для автоматического перевода
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        // Применяем перевод только для текстовых полей и muscle_groups
        if (in_array($key, ['name', 'description', 'instructions', 'muscle_groups']) && $this->translations) {
            $locale = app()->getLocale();
            
            // Если есть перевод для текущей локали, возвращаем его
            if (isset($this->translations[$locale][$key]) && !empty($this->translations[$locale][$key])) {
                return $this->translations[$locale][$key];
            }
        }
        
        return $value;
    }
    
    /**
     * Получить переведенное значение поля для конкретного языка
     * 
     * @param string $field - название поля (name, description, instructions)
     * @param string $locale - код языка (ru, uk, en)
     * @return string
     */
    public function getTranslated($field, $locale)
    {
        if ($this->translations && isset($this->translations[$locale][$field])) {
            return $this->translations[$locale][$field];
        }
        
        // Возвращаем оригинальное значение
        return parent::getAttribute($field) ?? '';
    }
    
    /**
     * Установить перевод для поля
     */
    public function setTranslation($field, $value, $locale)
    {
        $translations = $this->translations ?? [];
        
        if (!isset($translations[$locale])) {
            $translations[$locale] = [];
        }
        
        $translations[$locale][$field] = $value;
        $this->translations = $translations;
    }
    
    /**
     * Установить все переводы для языка
     */
    public function setTranslations($data, $locale)
    {
        $translations = $this->translations ?? [];
        $translations[$locale] = $data;
        $this->translations = $translations;
    }
    
    /**
     * Получить все переводы для языка
     */
    public function getTranslationsForLocale($locale)
    {
        return $this->translations[$locale] ?? [];
    }
    
    /**
     * Переопределяем toArray для правильной сериализации с переводами
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Применяем переводы для текстовых полей
        if ($this->translations) {
            $locale = app()->getLocale();
            
            $translatableFields = ['name', 'description', 'instructions', 'muscle_groups'];
            foreach ($translatableFields as $field) {
                if (isset($this->translations[$locale][$field]) && !empty($this->translations[$locale][$field])) {
                    $array[$field] = $this->translations[$locale][$field];
                }
            }
        }
        
        return $array;
    }
}