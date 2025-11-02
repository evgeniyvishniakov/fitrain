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
                'code' => 'UAH',
                'name' => 'Українська гривня',
                'symbol' => '₴',
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
                'exchange_rate' => 0.027,
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'symbol_position' => 'after',
                'decimal_places' => 2,
                'exchange_rate' => 0.025,
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
