<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\BaseController;
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
        }
        
        // Если роль не определена, показываем общий дашборд
        return view('crm.dashboard');
    }
}
