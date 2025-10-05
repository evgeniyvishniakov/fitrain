<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('crm.login');
        }

        $user = auth()->user();
        
        // Специальная логика для trainer: разрешаем доступ и Self-Athlete
        if ($role === 'trainer') {
            if (!$user->hasRole('trainer') && !$user->hasRole('self-athlete')) {
                abort(403, 'Доступ запрещен');
            }
        } else {
            if (!$user->hasRole($role)) {
                abort(403, 'Доступ запрещен');
            }
        }

        return $next($request);
    }
}
