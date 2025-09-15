<?php

namespace App\Http\Controllers\Crm\Auth;

use App\Http\Controllers\Crm\BaseController;
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
