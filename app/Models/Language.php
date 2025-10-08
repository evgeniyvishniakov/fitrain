<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'is_active',
        'is_default',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    /**
     * Скоупы
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Получить язык по умолчанию
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first() 
            ?? static::where('code', 'ru')->first()
            ?? static::first();
    }

    /**
     * Получить все активные языки
     */
    public static function getActive()
    {
        return static::active()->ordered()->get();
    }

    /**
     * Установить язык по умолчанию
     */
    public function setAsDefault()
    {
        // Снимаем флаг с других языков
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Устанавливаем текущий как дефолтный
        $this->update(['is_default' => true]);
    }

    /**
     * Отношения
     */
    public function users()
    {
        return $this->hasMany(User::class, 'language_code', 'code');
    }
}
















