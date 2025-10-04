<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'decimal_places',
        'exchange_rate',
        'is_active',
        'is_default',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:4',
        'decimal_places' => 'integer'
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
     * Получить валюту по умолчанию
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first() 
            ?? static::where('code', 'RUB')->first()
            ?? static::first();
    }

    /**
     * Получить все активные валюты
     */
    public static function getActive()
    {
        return static::active()->ordered()->get();
    }

    /**
     * Установить валюту по умолчанию
     */
    public function setAsDefault()
    {
        // Снимаем флаг с других валют
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Устанавливаем текущую как дефолтную
        $this->update(['is_default' => true]);
    }

    /**
     * Форматировать сумму
     */
    public function format($amount)
    {
        $formatted = number_format($amount, $this->decimal_places, '.', ' ');
        
        if ($this->symbol_position === 'before') {
            return $this->symbol . $formatted;
        }
        
        return $formatted . ' ' . $this->symbol;
    }

    /**
     * Конвертировать в другую валюту
     */
    public function convertTo($targetCurrency, $amount)
    {
        if ($this->code === $targetCurrency->code) {
            return $amount;
        }

        // Конвертируем в базовую валюту, затем в целевую
        $baseAmount = $amount / $this->exchange_rate;
        return $baseAmount * $targetCurrency->exchange_rate;
    }

    /**
     * Отношения
     */
    public function users()
    {
        return $this->hasMany(User::class, 'currency_code', 'code');
    }
}










