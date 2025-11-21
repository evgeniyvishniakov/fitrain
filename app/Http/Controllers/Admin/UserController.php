<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Shared\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    /**
     * Список всех пользователей
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Фильтрация по роли
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Фильтрация по статусу
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Поиск
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Форма создания пользователя
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Создание пользователя
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->assignRole($request->role);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно создан');
    }

    /**
     * Просмотр пользователя
     */
    public function show(User $user)
    {
        $user->load('roles');
        
        // Собираем активность для тренера
        $activities = collect();
        
        if ($user->hasRole('trainer')) {
            // Созданные тренировки
            $workouts = \App\Models\Trainer\Workout::where('trainer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function($workout) {
                    return [
                        'type' => 'workout_created',
                        'message' => 'Создал тренировку "' . ($workout->title ?? 'Без названия') . '"',
                        'date' => $workout->created_at,
                        'icon' => 'fa-dumbbell',
                        'color' => 'blue'
                    ];
                });
            
            // Созданные спортсмены
            $athletesCreated = User::where('trainer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function($athlete) {
                    return [
                        'type' => 'athlete_created',
                        'message' => 'Создал спортсмена "' . $athlete->name . '"',
                        'date' => $athlete->created_at,
                        'icon' => 'fa-user-plus',
                        'color' => 'green'
                    ];
                });
            
            // Изменения статуса спортсменов (последние обновления с изменением is_active)
            // Это упрощенная версия - показываем последние обновления спортсменов
            $athletesUpdated = User::where('trainer_id', $user->id)
                ->whereColumn('updated_at', '>', 'created_at')
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function($athlete) {
                    $status = $athlete->is_active ? 'активировал' : 'деактивировал';
                    return [
                        'type' => 'athlete_status_changed',
                        'message' => $status . ' спортсмена "' . $athlete->name . '"',
                        'date' => $athlete->updated_at,
                        'icon' => $athlete->is_active ? 'fa-check-circle' : 'fa-pause-circle',
                        'color' => $athlete->is_active ? 'green' : 'yellow'
                    ];
                });
            
            // Объединяем все активности и сортируем по дате
            $activities = $workouts
                ->concat($athletesCreated)
                ->concat($athletesUpdated)
                ->sortByDesc('date')
                ->take(30);
        }
        
        return view('admin.users.show', compact('user', 'activities'));
    }

    /**
     * Форма редактирования пользователя
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Обновление пользователя
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Обновляем роль
        $user->syncRoles([$request->role]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь успешно обновлен');
    }

    /**
     * Удаление пользователя
     */
    public function destroy(User $user)
    {
        // Нельзя удалить самого себя
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Нельзя удалить самого себя');
        }

        try {
            $user->delete();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Пользователь успешно удален');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('admin.users.index')
                    ->with('error', 'Невозможно удалить пользователя: к нему привязаны записи в системе (тренировки, шаблоны или другие данные). Удалите или переназначьте связанные данные и повторите попытку.');
            }

            throw $e;
        }
    }

    /**
     * Переключение статуса пользователя
     */
    public function toggleStatus(User $user)
    {
        // Нельзя деактивировать самого себя
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Нельзя изменить статус самого себя'], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
            'message' => $user->is_active ? 'Пользователь активирован' : 'Пользователь деактивирован'
        ]);
    }
}

