<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::updateOrCreate(
            ['name' => 'Базовый'],
            [
                'name' => 'Базовый',
                'description' => 'Базовая подписка для тренеров',
                'price' => 990.00,
                'currency_code' => 'UAH',
                'is_active' => true
            ]
        );

        $this->command->info('Создан план подписки');
    }
}
