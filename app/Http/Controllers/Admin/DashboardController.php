<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Главная страница админки
     */
    public function index()
    {
        return view('admin.home');
    }

    /**
     * Дашборд для админов
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
