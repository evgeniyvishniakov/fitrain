<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /**
     * Получить значение настройки
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value
        };
    }

    /**
     * Установить значение настройки
     */
    public static function set($key, $value, $type = 'string', $description = null, $isPublic = false)
    {
        $processedValue = match($type) {
            'boolean' => $value ? '1' : '0',
            'integer' => (string) $value,
            'json' => json_encode($value),
            default => (string) $value
        };

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $processedValue,
                'type' => $type,
                'description' => $description,
                'is_public' => $isPublic
            ]
        );
    }

    /**
     * Получить все публичные настройки
     */
    public static function getPublicSettings()
    {
        return static::where('is_public', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getValue()];
            });
    }

    /**
     * Получить обработанное значение
     */
    public function getValue()
    {
        return match($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value
        };
    }
}



