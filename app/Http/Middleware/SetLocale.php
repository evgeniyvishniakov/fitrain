<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);
        App::setLocale($locale);
        

        return $next($request);
    }

    /**
     * Определить локаль для приложения
     */
    private function getLocale(Request $request): string
    {
        // 1. Проверяем параметр в URL (?lang=ru)
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if ($this->isValidLocale($lang)) {
                Session::put('locale', $lang);
                return $lang;
            }
        }

        // 2. Проверяем сессию
        if (Session::has('locale')) {
            $lang = Session::get('locale');
            if ($this->isValidLocale($lang)) {
                return $lang;
            }
        }

        // 3. Проверяем язык авторизованного пользователя
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->language_code && $this->isValidLocale($user->language_code)) {
                // Устанавливаем язык пользователя в сессию для последующих запросов
                Session::put('locale', $user->language_code);
                return $user->language_code;
            }
        }

        // 4. Проверяем заголовок Accept-Language
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $preferredLocale = $this->getPreferredLocaleFromHeader($acceptLanguage);
            if ($preferredLocale) {
                return $preferredLocale;
            }
        }

        // 5. Возвращаем дефолтную локаль из базы данных
        try {
            $defaultLanguage = \App\Models\Language::where('is_default', true)->first();
            if ($defaultLanguage) {
                return $defaultLanguage->code;
            }
        } catch (\Exception $e) {
            // Если база данных недоступна, используем конфиг
        }
        
        return config('app.locale', 'ru');
    }

    /**
     * Проверить, является ли локаль валидной
     */
    private function isValidLocale(string $locale): bool
    {
        $supportedLocales = ['ru', 'en', 'ua'];
        return in_array($locale, $supportedLocales);
    }

    /**
     * Получить предпочтительную локаль из заголовка Accept-Language
     */
    private function getPreferredLocaleFromHeader(string $acceptLanguage): ?string
    {
        $locales = explode(',', $acceptLanguage);
        $supportedLocales = ['ru', 'en', 'ua'];

        foreach ($locales as $locale) {
            $locale = trim(explode(';', $locale)[0]);
            $locale = strtolower($locale);
            
            // Проверяем точное совпадение
            if (in_array($locale, $supportedLocales)) {
                return $locale;
            }
            
            // Проверяем только язык (без региона)
            $lang = explode('-', $locale)[0];
            if (in_array($lang, $supportedLocales)) {
                return $lang;
            }
        }

        return null;
    }
}
