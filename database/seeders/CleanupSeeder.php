<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Currency;

class CleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Удаляем все существующие языки и валюты
        Language::truncate();
        Currency::truncate();

        // Создаем только русский язык
        Language::create([
            'code' => 'ru',
            'name' => 'Русский',
            'native_name' => 'Русский',
            'flag' => '🇷🇺',
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1
        ]);

        // Создаем только рубль
        Currency::create([
            'code' => 'RUB',
            'name' => 'Российский рубль',
            'symbol' => '₽',
            'symbol_position' => 'after',
            'decimal_places' => 2,
            'exchange_rate' => 1.0000,
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1
        ]);

        $this->command->info('Создан только русский язык и рубль');
    }
}











