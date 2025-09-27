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
            $user = Auth::user();
            
            // Проверяем, что пользователь активен
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Ваш аккаунт деактивирован. Обратитесь к администратору.',
                ]);
            }

            // Проверяем, что у пользователя есть роль админа
            if (!$user->hasRole('admin')) {
                Auth::logout();
                
                // Перенаправляем в соответствующую панель
                if ($user->hasRole('trainer')) {
                    return redirect()->route('crm.dashboard')
                        ->with('info', 'Вы перенаправлены в панель тренера.');
                } elseif ($user->hasRole('athlete')) {
                    return redirect()->route('athlete.dashboard')
                        ->with('info', 'Вы перенаправлены в панель спортсмена.');
                } else {
                    return back()->withErrors([
                        'email' => 'У вас нет прав доступа к админ панели.',
                    ]);
                }
            }

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
