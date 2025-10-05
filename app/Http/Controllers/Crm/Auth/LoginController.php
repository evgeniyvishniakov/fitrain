<?php

namespace App\Http\Controllers\Crm\Auth;

use App\Http\Controllers\Crm\Shared\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    /**
     * Показать форму входа
     */
    public function showLoginForm()
    {
        return view('crm.auth.login');
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
            $request->session()->regenerate();
            
            // Редирект в зависимости от роли
            if (auth()->user()->hasRole('self-athlete')) {
                return redirect()->intended(route('crm.self-athlete.dashboard'));
            } elseif (auth()->user()->hasRole('trainer')) {
                return redirect()->intended(route('crm.trainer.dashboard'));
            } elseif (auth()->user()->hasRole('athlete')) {
                return redirect()->intended(route('crm.dashboard.main'));
            }
            
            return redirect()->intended(route('crm.dashboard.main'));
        }

        return back()->withErrors([
            'email' => 'Неверные учетные данные.',
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
