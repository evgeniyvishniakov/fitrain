<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Shared\User;
use App\Models\Trainer\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * Главная страница админки
     */
    public function index()
    {
        // Если пользователь не авторизован, показываем форму входа
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        // Если пользователь авторизован, перенаправляем на дашборд
        return redirect()->route('admin.dashboard.main');
    }

    /**
     * Дашборд для админов
     */
    public function dashboard()
    {
        // Статистика пользователей
        $usersStats = [
            'total' => User::count(),
            'trainers' => User::whereHas('roles', function($query) {
                $query->where('name', 'trainer');
            })->count(),
            'athletes' => User::whereHas('roles', function($query) {
                $query->where('name', 'athlete');
            })->count(),
            'admins' => User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];


        // Статистика тренировок
        $workoutsStats = [
            'total' => Workout::count(),
            'completed' => Workout::where('status', 'completed')->count(),
            'in_progress' => Workout::where('status', 'in_progress')->count(),
            'scheduled' => Workout::where('status', 'scheduled')->count(),
        ];

        // Последние регистрации
        $recentUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        // Активность по дням (последние 7 дней)
        $dailyActivity = Workout::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        return view('admin.dashboard', compact(
            'usersStats',
            'workoutsStats',
            'recentUsers',
            'dailyActivity'
        ));
    }
}
