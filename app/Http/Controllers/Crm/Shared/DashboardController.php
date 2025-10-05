<?php

namespace App\Http\Controllers\Crm\Shared;

use App\Http\Controllers\Crm\Shared\BaseController;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Главная страница CRM - перенаправление на вход
     */
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('crm.dashboard.main');
        }
        
        return redirect()->route('crm.login');
    }

    /**
     * Дашборд для авторизованных пользователей
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        if ($user->hasRole('trainer')) {
            return redirect()->route('crm.trainer.dashboard');
        } elseif ($user->hasRole('athlete')) {
            return redirect()->route('crm.athlete.dashboard');
        } elseif ($user->hasRole('self-athlete')) {
            return redirect()->route('crm.trainer.dashboard'); // Self-Athlete использует интерфейс тренера
        }
        
        // Если роль не определена, показываем общий дашборд
        return view('crm.dashboard');
    }
}
