<?php

namespace App\Http\Controllers\Crm\Auth;

use App\Http\Controllers\Crm\Shared\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Shared\User;
use App\Models\TrainerSubscription;
use App\Models\Currency;
use App\Models\SubscriptionPlan;
use App\Models\Language;
use Spatie\Permission\Models\Role;

class TrainerRegisterController extends BaseController
{
    /**
     * Показать форму регистрации тренера
     */
    public function showRegistrationForm()
    {
        $languages = Language::getActive();
        return view('crm.auth.trainer-register', compact('languages'));
    }

    /**
     * Обработка регистрации тренера
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'language_code' => 'required|string|exists:languages,code',
        ]);

        try {
            // Создаем тренера
            $trainer = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Автоматически хешируется через 'password' => 'hashed' в casts
                'is_active' => true,
                'email_verified_at' => now(), // Для будущей регистрации через Gmail
                'currency_code' => 'UAH', // По умолчанию UAH
                'language_code' => $request->language_code, // Выбранный язык
            ]);

            // Назначаем роль тренера
            $trainerRole = Role::where('name', 'trainer')->first();
            if ($trainerRole) {
                $trainer->assignRole($trainerRole);
            }

            // Создаем пробную подписку на 30 дней
            try {
                // Получаем валюту и план подписки
                $currency = Currency::where('code', 'UAH')->first();
                $plan = SubscriptionPlan::where('is_active', true)->first();
                
                TrainerSubscription::create([
                    'trainer_id' => $trainer->id,
                    'subscription_plan_id' => $plan?->id,
                    'status' => 'trial',
                    'price' => 0,
                    'currency_code' => $currency?->code ?? 'UAH',
                    'start_date' => now(),
                    'expires_date' => now()->addDays(30),
                    'is_trial' => true,
                    'trial_days' => 30,
                    'notes' => 'Пробный период при регистрации',
                ]);
            } catch (\Exception $e) {
                \Log::warning('Не удалось создать подписку для тренера: ' . $e->getMessage());
                // Продолжаем регистрацию даже если не удалось создать подписку
            }

            // Авторизуем тренера и устанавливаем язык в сессию
            Auth::login($trainer);
            \Illuminate\Support\Facades\Session::put('locale', $trainer->language_code);

            return redirect()->route('crm.trainer.dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Ошибка регистрации тренера: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => __('auth.registration_error')
            ])->withInput();
        }
    }
}
