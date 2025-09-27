<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Shared\User;
use App\Models\Trainer\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends BaseController
{
    /**
     * Главная страница статистики
     */
    public function index()
    {
        // Общая статистика
        $stats = [
            'users' => [
                'total' => User::count(),
                'trainers' => User::whereHas('roles', function($query) {
                    $query->where('name', 'trainer');
                })->count(),
                'athletes' => User::whereHas('roles', function($query) {
                    $query->where('name', 'athlete');
                })->count(),
                'active' => User::where('is_active', true)->count(),
            ],
            'workouts' => [
                'total' => Workout::count(),
                'completed' => Workout::where('status', 'completed')->count(),
                'in_progress' => Workout::where('status', 'in_progress')->count(),
            ],
            'growth' => [
                'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'workouts_this_month' => Workout::whereMonth('created_at', now()->month)->count(),
            ]
        ];

        // Регистрации по месяцам (последние 12 месяцев)
        $userGrowth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Топ тренеров по количеству спортсменов
        $topTrainers = User::whereHas('roles', function($query) {
                $query->where('name', 'trainer');
            })
            ->withCount('athletes')
            ->orderBy('athletes_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.statistics.index', compact('stats', 'userGrowth', 'topTrainers'));
    }

    /**
     * Статистика пользователей
     */
    public function users()
    {
        // Регистрации по дням (последние 30 дней)
        $dailyRegistrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        // Распределение по ролям
        $roleDistribution = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.id', 'roles.name')
            ->get();

        // Активность пользователей
        $userActivity = User::select(
                DB::raw('DATE(last_login_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('last_login_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(last_login_at)'))
            ->orderBy(DB::raw('DATE(last_login_at)'))
            ->get();

        return view('admin.statistics.users', compact('dailyRegistrations', 'roleDistribution', 'userActivity'));
    }

    /**
     * Статистика тренировок
     */
    public function workouts()
    {
        // Тренировки по дням (последние 30 дней)
        $dailyWorkouts = Workout::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        // Статистика по статусам
        $statusStats = Workout::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        // Топ тренеров по тренировкам
        $topTrainersByWorkouts = User::whereHas('roles', function($query) {
                $query->where('name', 'trainer');
            })
            ->withCount('trainerWorkouts')
            ->orderBy('trainer_workouts_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.statistics.workouts', compact('dailyWorkouts', 'statusStats', 'topTrainersByWorkouts'));
    }

    /**
     * Экспорт статистики
     */
    public function export($type)
    {
        switch ($type) {
            case 'users':
                return $this->exportUsers();
            case 'workouts':
                return $this->exportWorkouts();
            default:
                abort(404);
        }
    }

    private function exportUsers()
    {
        $users = User::with('roles')->get();
        
        $csv = "ID,Имя,Email,Роль,Активен,Дата регистрации\n";
        
        foreach ($users as $user) {
            $csv .= implode(',', [
                $user->id,
                '"' . $user->name . '"',
                $user->email,
                $user->roles->pluck('name')->implode(', '),
                $user->is_active ? 'Да' : 'Нет',
                $user->created_at->format('Y-m-d H:i:s')
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="users_export_' . date('Y-m-d') . '.csv"');
    }

    private function exportWorkouts()
    {
        $workouts = Workout::with(['trainer', 'athlete'])->get();
        
        $csv = "ID,Тренер,Спортсмен,Статус,Дата создания\n";
        
        foreach ($workouts as $workout) {
            $csv .= implode(',', [
                $workout->id,
                '"' . ($workout->trainer->name ?? 'N/A') . '"',
                '"' . ($workout->athlete->name ?? 'N/A') . '"',
                $workout->status,
                $workout->created_at->format('Y-m-d H:i:s')
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="workouts_export_' . date('Y-m-d') . '.csv"');
    }
}
