<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\BaseController;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Главная страница CRM
     */
    public function index()
    {
        return view('crm.home');
    }

    /**
     * Дашборд для авторизованных пользователей
     */
    public function dashboard()
    {
        return view('crm.dashboard');
    }
}
