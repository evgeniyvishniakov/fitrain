<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shared\User;
use App\Models\Language;
use App\Models\Currency;

class SetDefaultUserLanguages extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем дефолтные язык и валюту
        $defaultLanguage = Language::where('is_default', true)->first();
        $defaultCurrency = Currency::where('is_default', true)->first();

        if (!$defaultLanguage) {
            $defaultLanguage = Language::where('code', 'ru')->first();
        }

        if (!$defaultCurrency) {
            $defaultCurrency = Currency::where('code', 'RUB')->first();
        }

        // Обновляем пользователей без языка и валюты
        $updated = User::whereNull('language_code')
            ->orWhereNull('currency_code')
            ->update([
                'language_code' => $defaultLanguage->code,
                'currency_code' => $defaultCurrency->code
            ]);

        $this->command->info("Обновлено {$updated} пользователей с дефолтными языком и валютой");
    }
}
