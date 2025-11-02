<?php

namespace App\Http\Controllers\Crm\Auth;

use App\Http\Controllers\Crm\Shared\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;

class LoginController extends BaseController
{
    /**
     * Показать форму входа
     */
    public function showLoginForm()
    {
        $languages = Language::getActive();
        return view('crm.auth.login', compact('languages'));
    }

    /**
     * Обработка входа
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Проверяем, что пользователь активен
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => __('auth.account_deactivated'),
                ]);
            }

            $request->session()->regenerate();
            
            // Редирект в зависимости от роли
            if ($user->hasRole('self-athlete')) {
                return redirect()->intended(route('crm.self-athlete.dashboard'));
            } elseif ($user->hasRole('trainer')) {
                return redirect()->intended(route('crm.trainer.dashboard'));
            } elseif ($user->hasRole('athlete')) {
                return redirect()->intended(route('crm.dashboard.main'));
            }
            
            return redirect()->intended(route('crm.dashboard.main'));
        }

        return back()->withErrors([
            'email' => __('auth.invalid_credentials'),
        ]);
    }

    /**
     * Выход из системы
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('crm.login');
    }
}
