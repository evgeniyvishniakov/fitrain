<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Переключить язык интерфейса
     */
    public function switch(Request $request)
    {
        $locale = $request->get('locale');
        
        // Проверяем, что язык поддерживается
        $supportedLocales = ['ru', 'en', 'ua'];
        if (!in_array($locale, $supportedLocales)) {
            return response()->json([
                'success' => false,
                'message' => __('common.error')
            ], 400);
        }

        // Сохраняем язык в сессии
        Session::put('locale', $locale);
        
        // Если пользователь авторизован - сохраняем язык в базу данных
        if (auth()->check()) {
            $user = auth()->user();
            $user->language_code = $locale;
            $user->save();
        }
        
        // Устанавливаем локаль для текущего запроса
        App::setLocale($locale);

        return response()->json([
            'success' => true,
            'message' => __('common.success'),
            'locale' => $locale
        ]);
    }

    /**
     * Получить текущий язык
     */
    public function current()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'supported_locales' => ['ru', 'en', 'ua']
        ]);
    }
}