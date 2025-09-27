<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, что пользователь авторизован
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Проверяем, что пользователь активен
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Ваш аккаунт деактивирован. Обратитесь к администратору.']);
        }

        // Проверяем, что у пользователя есть роль админа
        if (!$user->hasRole('admin')) {
            // Если пользователь не админ, перенаправляем его в соответствующую панель
            if ($user->hasRole('trainer')) {
                return redirect()->route('crm.dashboard');
            } elseif ($user->hasRole('athlete')) {
                return redirect()->route('athlete.dashboard');
            } else {
                Auth::logout();
                return redirect()->route('admin.login')
                    ->withErrors(['email' => 'У вас нет прав доступа к админ панели.']);
            }
        }

        return $next($request);
    }
}

