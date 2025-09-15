<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Landing\BaseController;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    /**
     * Главная страница лендинга
     */
    public function index()
    {
        return view('landing.home');
    }

    /**
     * Страница с ценами
     */
    public function pricing()
    {
        return view('landing.pricing');
    }

    /**
     * Страница с функциями
     */
    public function features()
    {
        return view('landing.features');
    }

    /**
     * О нас
     */
    public function about()
    {
        return view('landing.about');
    }

    /**
     * Контакты
     */
    public function contact()
    {
        return view('landing.contact');
    }
}
