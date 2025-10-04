<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'RUB',
                'name' => 'Российский рубль',
                'symbol' => '₽',
                'symbol_position' => 'after',
                'decimal_places' => 2,
                'exchange_rate' => 1.0000,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'exchange_rate' => 0.011,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2
            ],
            [
                'code' => 'UAH',
                'name' => 'Українська гривня',
                'symbol' => '₴',
                'symbol_position' => 'after',
                'decimal_places' => 2,
                'exchange_rate' => 0.42,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($currencies as $currencyData) {
            Currency::updateOrCreate(
                ['code' => $currencyData['code']],
                $currencyData
            );
        }

        $this->command->info('Создано ' . count($currencies) . ' валют');
    }
}
