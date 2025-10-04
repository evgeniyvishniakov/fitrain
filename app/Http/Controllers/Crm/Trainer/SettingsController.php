<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends BaseController
{
    /**
     * Показать страницу настроек
     */
    public function index()
    {
        $user = Auth::user();
        return view('crm.trainer.settings.index', compact('user'));
    }

    /**
     * Обновить профиль
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
        ]);

        $user = Auth::user();
        $user->update($request->only([
            'name', 'email', 'phone', 'bio', 'specialization', 'experience_years'
        ]));

        return redirect()->back()->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Обновить пароль
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Текущий пароль неверен']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Пароль успешно изменен');
    }

    /**
     * Обновить настройки безопасности
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'two_factor_enabled' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);

        $user = Auth::user();
        $user->update($request->only([
            'two_factor_enabled', 'email_notifications', 'sms_notifications'
        ]));

        return redirect()->back()->with('success', 'Настройки безопасности обновлены');
    }

    /**
     * Обновить настройки языка и валюты
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'language_code' => 'required|string|exists:languages,code',
            'currency_code' => 'required|string|exists:currencies,code',
            'timezone' => 'required|string|max:50',
        ]);

        $user = Auth::user();
        $user->update($request->only([
            'language_code', 'currency_code', 'timezone'
        ]));

        // Обновляем локаль в сессии для немедленного применения
        if ($request->has('language_code')) {
            session(['locale' => $request->get('language_code')]);
        }

        return redirect()->back()->with('success', 'Настройки языка и валюты обновлены');
    }

    /**
     * Обновить настройки уведомлений
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_workout_reminders' => 'boolean',
            'email_payment_reminders' => 'boolean',
            'email_athlete_progress' => 'boolean',
            'email_system_updates' => 'boolean',
            'push_workout_reminders' => 'boolean',
            'push_payment_reminders' => 'boolean',
            'push_athlete_progress' => 'boolean',
        ]);

        $user = Auth::user();
        $user->update($request->only([
            'email_workout_reminders', 'email_payment_reminders', 'email_athlete_progress',
            'email_system_updates', 'push_workout_reminders', 'push_payment_reminders',
            'push_athlete_progress'
        ]));

        return redirect()->back()->with('success', 'Настройки уведомлений обновлены');
    }
}
