<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubdomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $subdomain = $this->getSubdomain($host);
        
        // Добавляем информацию о поддомене в request
        $request->attributes->set('subdomain', $subdomain);
        
        return $next($request);
    }
    
    /**
     * Извлекает поддомен из хоста
     */
    private function getSubdomain(string $host): string
    {
        $parts = explode('.', $host);
        
        // Для локальной разработки с .local
        if (str_contains($host, '.local')) {
            if (count($parts) >= 3) {
                return $parts[0];
            }
            return 'main';
        }
        
        // Для продакшена с .app
        if (str_contains($host, '.app')) {
            if (count($parts) >= 3) {
                return $parts[0];
            }
            return 'main';
        }
        
        // Fallback для localhost
        if ($host === 'localhost' || $host === '127.0.0.1') {
            return 'main';
        }
        
        return 'main';
    }
}
