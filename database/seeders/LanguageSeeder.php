<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'code' => 'ru',
                'name' => 'Русский',
                'native_name' => 'Русский',
                'flag' => '🇷🇺',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'flag' => '🇺🇸',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2
            ],
            [
                'code' => 'ua',
                'name' => 'Українська',
                'native_name' => 'Українська',
                'flag' => '🇺🇦',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($languages as $languageData) {
            Language::updateOrCreate(
                ['code' => $languageData['code']],
                $languageData
            );
        }

        $this->command->info('Создано ' . count($languages) . ' языков');
    }
}
