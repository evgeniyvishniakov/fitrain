<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Cache flag to avoid checking schema on every request.
     */
    protected static ?bool $columnExists = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = Auth::user();

        if ($user && $this->shouldRecord($request) && $this->hasColumn()) {
            $user->forceFill([
                'last_activity_at' => now(),
            ])->saveQuietly();
        }

        return $response;
    }

    /**
     * Determine if the current request should be recorded as an activity.
     */
    protected function shouldRecord(Request $request): bool
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return false;
        }

        return true;
    }

    /**
     * Ensure the column exists before attempting to write.
     */
    protected function hasColumn(): bool
    {
        if (static::$columnExists === null) {
            static::$columnExists = Schema::hasColumn('users', 'last_activity_at');
        }

        return static::$columnExists;
    }
}

