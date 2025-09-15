<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    /**
     * Показать форму входа в админку
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Обработка входа в админку
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard.main'));
        }

        return back()->withErrors([
            'email' => 'Неверные учетные данные.',
        ]);
    }

    /**
     * Выход из админки
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
