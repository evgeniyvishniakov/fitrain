<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Trainer;
use App\Models\Athlete\Athlete;
use App\Models\Trainer\Workout;
use Illuminate\Http\Request;

class TrainerController extends BaseController
{
    /**
     * Извлекает количество тренировок из описания платежа
     */
    private function extractSessionsFromDescription($description)
    {
        if (empty($description)) {
            return 0;
        }
        
        // Основной вариант: "4 тренировки", "8 тренировок", "12 тренировок" (RU)
        if (preg_match('/(\d+)\s+(?:тренировка|тренировки|тренировок)/ui', $description, $matches)) {
            return (int) $matches[1];
        }
        
        // Вариант с "тренирки": "4 тренирки", "8 тренирки"
        if (preg_match('/(\d+)\s*тренирки/ui', $description, $matches)) {
            return (int) $matches[1];
        }
        
        // Украинский вариант: "4 тренування", "8 тренувань", "12 тренувань"
        if (preg_match('/(\d+)\s+(?:тренування|тренувань)/ui', $description, $matches)) {
            return (int) $matches[1];
        }
        
        // Английский вариант: "4 workouts", "8 workouts", "12 workouts"
        if (preg_match('/(\d+)\s+workouts?/ui', $description, $matches)) {
            return (int) $matches[1];
        }
        
        // Разовая тренировка (RU)
        if (preg_match('/Разовая\s*тренировка/ui', $description)) {
            return 1;
        }
        
        // Разове тренування (UA)
        if (preg_match('/Разове\s*тренування/ui', $description)) {
            return 1;
        }
        
        // Single workout (EN)
        if (preg_match('/Single\s*workout/ui', $description)) {
            return 1;
        }
        
        // Безлимит (RU)
        if (preg_match('/Безлимит/ui', $description)) {
            return 30;
        }
        
        // Безліміт (UA)
        if (preg_match('/Безліміт/ui', $description)) {
            return 30;
        }
        
        // Unlimited (EN)
        if (preg_match('/Unlimited/ui', $description)) {
            return 30;
        }
        
        return 0;
    }

    /**
     * Приводит старые значения уровня спортсмена к актуальным
     */
    private function normalizeSportLevel(?string $level): ?string
    {
        if (!$level) {
            return $level;
        }

        $mapping = [
            'amateur' => 'intermediate',
            'pro' => 'advanced',
        ];

        return $mapping[$level] ?? $level;
    }
    
    public function dashboard()
    {
        $trainer = auth()->user();
        
        // Для Self-Athlete считаем только себя, для тренера - всех спортсменов
        if ($trainer->hasRole('self-athlete')) {
            $athletes = 1; // Self-Athlete сам себе спортсмен
        } else {
            $athletes = $trainer->athletes()->count();
        }
        
        // Для Self-Athlete тренировки - это его собственные тренировки
        if ($trainer->hasRole('self-athlete')) {
            $workouts = \App\Models\Trainer\Workout::where('athlete_id', $trainer->id)->count();
            $todayWorkouts = \App\Models\Trainer\Workout::where('athlete_id', $trainer->id)
                ->whereDate('date', now()->format('Y-m-d'))
                ->count();
            $completedThisMonth = \App\Models\Trainer\Workout::where('athlete_id', $trainer->id)
                ->where('status', 'completed')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count();
            $recentWorkouts = \App\Models\Trainer\Workout::where('athlete_id', $trainer->id)->latest()->take(5)->get();
        } else {
            // Общее количество тренировок
            $workouts = $trainer->trainerWorkouts()->count();
            
            // Тренировки на сегодня
            $todayWorkouts = $trainer->trainerWorkouts()
                ->whereDate('date', now()->format('Y-m-d'))
                ->count();
            
            // Завершенные тренировки за месяц
            $completedThisMonth = $trainer->trainerWorkouts()
                ->where('status', 'completed')
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count();
            
            $recentWorkouts = $trainer->trainerWorkouts()->with('athlete')->latest()->take(5)->get();
        }
        
        // Данные для календаря (берем тренировки за последние 3 месяца)
        $startDate = now()->subMonths(3)->format('Y-m-d');
        $endDate = now()->addMonths(3)->format('Y-m-d');
        
        if ($trainer->hasRole('self-athlete')) {
            $monthWorkouts = \App\Models\Trainer\Workout::where('athlete_id', $trainer->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->select('id', 'title', 'date', 'time', 'status', 'athlete_id')
                ->get()
                ->map(function($workout) use ($trainer) {
                    return [
                        'id' => $workout->id,
                        'title' => $workout->title,
                        'date' => \Carbon\Carbon::parse($workout->date)->format('Y-m-d'), // Форматируем дату без времени
                        'time' => $workout->time,
                        'status' => $workout->status,
                        'athlete_name' => $trainer->name // Self-Athlete сам себе спортсмен
                    ];
                });
            
            // Ближайшие тренировки (сегодня и завтра)
            $today = now()->format('Y-m-d');
            $tomorrow = now()->addDay()->format('Y-m-d');
            
            $upcomingWorkouts = \App\Models\Trainer\Workout::where('athlete_id', $trainer->id)
                ->whereIn('date', [$today, $tomorrow])
                ->where('status', '!=', 'cancelled')
                ->orderBy('date')
                ->orderBy('time')
                ->get();
        } else {
            $monthWorkouts = $trainer->trainerWorkouts()
                ->whereBetween('date', [$startDate, $endDate])
                ->with('athlete:id,name')
                ->select('id', 'title', 'date', 'time', 'status', 'athlete_id')
                ->get()
                ->map(function($workout) {
                    return [
                        'id' => $workout->id,
                        'title' => $workout->title,
                        'date' => \Carbon\Carbon::parse($workout->date)->format('Y-m-d'), // Форматируем дату без времени
                        'time' => $workout->time,
                        'status' => $workout->status,
                        'athlete_name' => $workout->athlete ? $workout->athlete->name : 'Неизвестно'
                    ];
                });
            
            // Ближайшие тренировки (сегодня и завтра)
            $today = now()->startOfDay();
            $tomorrow = now()->addDay()->startOfDay();
            $dayAfterTomorrow = now()->addDays(2)->startOfDay();
            
            $upcomingWorkouts = $trainer->trainerWorkouts()
                ->with('athlete:id,name')
                ->where(function($query) use ($today, $tomorrow) {
                    $query->whereDate('date', '>=', $today)
                          ->whereDate('date', '<=', $tomorrow);
                })
                ->where('status', '!=', 'cancelled')
                ->orderBy('date')
                ->orderBy('time')
                ->get();
        }
        
        return view('crm.trainer.dashboard', compact(
            'trainer', 
            'athletes', 
            'workouts', 
            'todayWorkouts',
            'completedThisMonth',
            'recentWorkouts',
            'monthWorkouts',
            'upcomingWorkouts'
        ));
    }
    
    public function profile()
    {
        $trainer = auth()->user();
        return view('crm.trainer.profile', compact('trainer'));
    }
    
    public function updateProfile(Request $request)
    {
        $trainer = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $trainer->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
        ]);
        
        $trainer->update($request->all());
        
        return redirect()->route('crm.trainer.profile')->with('success', 'Профиль обновлен');
    }
    
    public function athletes()
    {
        $trainer = auth()->user();
        $athletes = $trainer->athletes()->with(['workouts' => function($query) {
            $query->latest();
        }, 'workouts.exercises' => function($query) {
            $query->select('exercises.id', 'exercises.name', 'exercises.category', 'exercises.equipment', 'exercises.image_url', 'exercises.image_url_2', 'exercises.video_url', 'exercises.fields_config', 'workout_exercise.*')
                  ->orderBy('workout_exercise.order_index', 'asc');
        }])->paginate(12);
        
        // Добавляем финансовые данные для каждого спортсмена из новой таблицы
        $athletes->getCollection()->transform(function ($athlete) {
            // Получаем финансовые данные из таблицы trainer_finances
            $finance = \App\Models\TrainerFinance::where('trainer_id', auth()->id())
                ->where('athlete_id', $athlete->id)
                ->first();
            
            if ($finance) {
                // Вычисляем общее количество тренировок из истории платежей
                $totalSessionsFromHistory = 0;
                $paymentHistory = $finance->payment_history ?? [];
                
                foreach ($paymentHistory as $payment) {
                    $description = $payment['description'] ?? '';
                    $sessions = $this->extractSessionsFromDescription($description);
                    $totalSessionsFromHistory += $sessions;
                }
                
                $totalSessions = $totalSessionsFromHistory ?: $finance->total_sessions;
                
                $athlete->finance = [
                    'id' => $athlete->id,
                    'package_type' => $finance->package_type,
                    'total_sessions' => $totalSessions,
                    'used_sessions' => $finance->used_sessions,
                    'remaining_sessions' => $totalSessions - $finance->used_sessions,
                    'package_price' => $finance->package_price,
                    'purchase_date' => $finance->purchase_date,
                    'expires_date' => $finance->expires_date,
                    'status' => $totalSessions > 0 ? 'active' : 'inactive',
                    'total_paid' => $finance->total_paid,
                    'last_payment_date' => $finance->last_payment_date,
                    'payment_history' => $paymentHistory
                ];
            } else {
                // Если нет финансовых данных, создаем пустую структуру
                $athlete->finance = [
                    'id' => $athlete->id,
                    'package_type' => null,
                    'total_sessions' => 0,
                    'used_sessions' => 0,
                    'remaining_sessions' => 0,
                    'package_price' => 0,
                    'purchase_date' => null,
                    'expires_date' => null,
                    'status' => 'inactive',
                    'total_paid' => 0,
                    'last_payment_date' => null,
                    'payment_history' => []
                ];
            }
            
            return $athlete;
        });
        
        $response = response()->view('crm.trainer.athletes.index', compact('athletes'));
        
        // Добавляем заголовки для предотвращения кеширования
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
    }
    
    public function addAthlete()
    {
        return view('crm.trainer.athletes.create');
    }
    
    public function storeAthlete(Request $request)
    {
        // Проверяем, что запрос JSON
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Expected JSON request'], 400);
        }
        
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'weight' => 'nullable|numeric|min:0',
                'height' => 'nullable|numeric|min:0',
                'sport_level' => 'nullable|in:beginner,intermediate,advanced,professional,amateur,pro',
                'goals' => 'nullable|array',
                'health_restrictions' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        }
        
        // Вычисляем возраст из даты рождения
        $age = null;
        if ($request->birth_date) {
            $age = \Carbon\Carbon::parse($request->birth_date)->age;
        }
        
        try {
            $athlete = Athlete::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'age' => $age,
                'gender' => $request->gender,
                'weight' => $request->weight,
                'height' => $request->height,
                'sport_level' => $this->normalizeSportLevel($request->sport_level),
                'goals' => $request->goals ? json_encode($request->goals) : null,
                'health_restrictions' => $request->health_restrictions ? json_encode([['type' => 'Общие ограничения', 'description' => $request->health_restrictions]]) : null,
                'is_active' => $request->is_active ?? true,
                'trainer_id' => auth()->id(),
            ]);
            
            // Назначаем роль спортсмену (гарантированно)
            try {
                $athleteRole = \Spatie\Permission\Models\Role::firstOrCreate([
                    'name' => 'athlete',
                    'guard_name' => 'web',
                ]);
                // Привязываем только эту роль (без дубликатов)
                $athlete->syncRoles(['athlete']);
            } catch (\Exception $e) {
                \Log::error('Ошибка назначения роли athlete новому спортсмену: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Спортсмен успешно создан',
                'athlete' => $athlete
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка создания спортсмена: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function showAthlete($id)
    {
        $athlete = Athlete::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        // Загружаем связанные данные
        $athlete->load([
            'workouts' => function($query) {
                $query->latest()->take(10);
            },
            'progress' => function($query) {
                $query->latest()->take(20);
            }
        ]);
        
        return view('crm.trainer.athletes.show', compact('athlete'));
    }
    
    public function editAthlete($id)
    {
        $athlete = Athlete::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        return view('crm.trainer.athletes.edit', compact('athlete'));
    }
    
    public function updateAthlete(Request $request, $id)
    {
        // Логируем запрос для отладки
        \Log::info('Update Athlete Request:', [
            'id' => $id,
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->roles->pluck('name'),
            'data' => $request->all(),
            'is_json' => $request->isJson(),
            'expects_json' => $request->expectsJson(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        $athlete = Athlete::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $athlete->id,
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'password' => 'nullable|string|min:8',
            'sport_level' => 'nullable|in:beginner,intermediate,advanced,professional,amateur,pro',
        ]);
        
        $updateData = $request->all();
        
        // Если пароль указан, хешируем его
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        } else {
            // Убираем пароль из данных обновления, если он не указан
            unset($updateData['password']);
        }
        
        if (array_key_exists('sport_level', $updateData)) {
            $updateData['sport_level'] = $this->normalizeSportLevel($updateData['sport_level']);
        }

        $athlete->update($updateData);
        
        // Всегда возвращаем JSON для AJAX запросов
        return response()->json([
            'success' => true,
            'message' => 'Данные спортсмена обновлены',
            'athlete' => $athlete->fresh()
        ]);
    }
    
    public function removeAthlete($id)
    {
        $athlete = Athlete::findOrFail($id);
        
        if ($athlete->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $athlete->delete();
        
        // Возвращаем JSON для AJAX запросов
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Спортсмен успешно удален'
            ]);
        }
        
        return redirect()->route('crm.trainer.athletes')->with('success', 'Спортсмен удален');
    }
    
    // Сохранение измерения
    public function storeMeasurement(Request $request, $id)
    {
        $athlete = Athlete::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $request->validate([
            'measurement_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0',
            'water_percentage' => 'nullable|numeric|min:0|max:100',
            'chest' => 'nullable|numeric|min:0',
            'waist' => 'nullable|numeric|min:0',
            'hips' => 'nullable|numeric|min:0',
            'bicep' => 'nullable|numeric|min:0',
            'thigh' => 'nullable|numeric|min:0',
            'neck' => 'nullable|numeric|min:0',
            'resting_heart_rate' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|integer|min:0',
            'blood_pressure_diastolic' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $measurement = $athlete->measurements()->create([
            'measurement_date' => $request->measurement_date,
            'weight' => $request->weight,
            'height' => $request->height,
            'body_fat_percentage' => $request->body_fat_percentage,
            'muscle_mass' => $request->muscle_mass,
            'water_percentage' => $request->water_percentage,
            'chest' => $request->chest,
            'waist' => $request->waist,
            'hips' => $request->hips,
            'bicep' => $request->bicep,
            'thigh' => $request->thigh,
            'neck' => $request->neck,
            'resting_heart_rate' => $request->resting_heart_rate,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'notes' => $request->notes,
            'measured_by' => auth()->id(),
        ]);
        
        // Обновляем профиль спортсмена с новыми весом и ростом
        if ($request->weight) {
            $athlete->weight = $request->weight;
        }
        if ($request->height) {
            $athlete->height = $request->height;
        }
        $athlete->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Измерение успешно сохранено',
            'measurement' => $measurement
        ]);
    }
    
    // Получение измерений спортсмена
    public function getMeasurements($id)
    {
        $athlete = Athlete::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $measurements = $athlete->measurements()
            ->orderBy('measurement_date', 'desc')
            ->get();
        
        // Получаем финансовые данные из новой таблицы
        $finance = \App\Models\TrainerFinance::where('trainer_id', auth()->id())
            ->where('athlete_id', $athlete->id)
            ->first();
            
        // Если нет записи, создаем пустую
        if (!$finance) {
            $finance = \App\Models\TrainerFinance::create([
                'trainer_id' => auth()->id(),
                'athlete_id' => $athlete->id,
                'package_type' => null,
                'total_sessions' => 0,
                'used_sessions' => 0,
                'package_price' => 0,
                'purchase_date' => null,
                'expires_date' => null,
                'payment_method' => null,
                'payment_description' => null,
                'total_paid' => 0,
                'last_payment_date' => null,
                'payment_history' => []
            ]);
        }

        // Пересчитываем финансовые данные из истории платежей
        $financeData = null;
        if ($finance) {
            $totalSessionsFromHistory = 0;
            $paymentHistory = $finance->payment_history ?? [];
            
            foreach ($paymentHistory as $payment) {
                $description = $payment['description'] ?? '';
                $sessions = $this->extractSessionsFromDescription($description);
                $totalSessionsFromHistory += $sessions;
            }
            
            $totalSessions = $totalSessionsFromHistory ?: $finance->total_sessions;
            
            
            $financeData = [
                'total_paid' => $finance->total_paid,
                'payment_history' => $paymentHistory,
                'last_payment_date' => $finance->last_payment_date,
                'package_type' => $finance->package_type,
                'total_sessions' => $totalSessions,
                'used_sessions' => $finance->used_sessions,
                'remaining_sessions' => $totalSessions - $finance->used_sessions,
                'package_price' => $finance->package_price,
                'purchase_date' => $finance->purchase_date,
                'expires_date' => $finance->expires_date,
                'payment_method' => $finance->payment_method,
                'payment_description' => $finance->payment_description,
            ];
            
            // Временная отладка
            \Log::info('Returning finance data for athlete ' . $athlete->id, [
                'payment_history_count' => count($paymentHistory),
                'payment_history' => $paymentHistory
            ]);
        }

        return response()->json([
            'success' => true,
            'measurements' => $measurements,
            'athlete' => [
                'id' => $athlete->id,
                'name' => $athlete->name,
                'weight' => $athlete->weight,
                'height' => $athlete->height,
                'email' => $athlete->email,
                'birth_date' => $athlete->birth_date,
                'gender' => $athlete->gender,
                'sport_level' => $athlete->sport_level,
                'goals' => $athlete->goals,
                'contact_info' => $athlete->contact_info,
                'health_restrictions' => $athlete->health_restrictions,
                'medical_documents' => $athlete->medical_documents,
                'last_medical_checkup' => $athlete->last_medical_checkup,
                'profile_modules' => $athlete->profile_modules,
                'is_active' => $athlete->is_active,
                'created_at' => $athlete->created_at,
                'updated_at' => $athlete->updated_at,
                'finance' => $financeData
            ]
        ]);
    }
    
    // Обновление измерения
    public function updateMeasurement(Request $request, $athleteId, $measurementId)
    {
        $athlete = Athlete::where('id', $athleteId)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $measurement = $athlete->measurements()
            ->where('id', $measurementId)
            ->firstOrFail();
        
        $request->validate([
            'measurement_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0',
            'water_percentage' => 'nullable|numeric|min:0|max:100',
            'chest' => 'nullable|numeric|min:0',
            'waist' => 'nullable|numeric|min:0',
            'hips' => 'nullable|numeric|min:0',
            'bicep' => 'nullable|numeric|min:0',
            'thigh' => 'nullable|numeric|min:0',
            'neck' => 'nullable|numeric|min:0',
            'resting_heart_rate' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|integer|min:0',
            'blood_pressure_diastolic' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $measurement->update([
            'measurement_date' => $request->measurement_date,
            'weight' => $request->weight,
            'height' => $request->height,
            'body_fat_percentage' => $request->body_fat_percentage,
            'muscle_mass' => $request->muscle_mass,
            'water_percentage' => $request->water_percentage,
            'chest' => $request->chest,
            'waist' => $request->waist,
            'hips' => $request->hips,
            'bicep' => $request->bicep,
            'thigh' => $request->thigh,
            'neck' => $request->neck,
            'resting_heart_rate' => $request->resting_heart_rate,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'notes' => $request->notes,
        ]);
        
        // Обновляем профиль спортсмена с новыми весом и ростом
        if ($request->weight) {
            $athlete->weight = $request->weight;
        }
        if ($request->height) {
            $athlete->height = $request->height;
        }
        $athlete->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Измерение успешно обновлено',
            'measurement' => $measurement
        ]);
    }
    
    // Удаление измерения
    public function deleteMeasurement($athleteId, $measurementId)
    {
        $athlete = Athlete::where('id', $athleteId)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $measurement = $athlete->measurements()
            ->where('id', $measurementId)
            ->firstOrFail();
        
        $measurement->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Измерение успешно удалено'
        ]);
    }
    
    /**
     * Получение прогресса упражнений для тренера
     */
    public function getExerciseProgress(Request $request)
    {
        $workoutId = $request->get('workout_id');
        
        if (!$workoutId) {
            return response()->json(['error' => 'workout_id is required'], 400);
        }
        
        // Получаем прогресс упражнений для указанной тренировки
        $progress = \App\Models\Athlete\ExerciseProgress::where('workout_id', $workoutId)->get();
        
        return response()->json($progress);
    }
    
    /**
     * Обновление прогресса упражнений для тренера
     */
    public function updateExerciseProgress(Request $request)
    {
        \Log::info('TrainerController::updateExerciseProgress called', [
            'workout_id' => $request->get('workout_id'),
            'exercises' => $request->get('exercises'),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->roles?->first()?->name
        ]);
        
        $request->validate([
            'workout_id' => 'required|integer',
            'exercises' => 'required|array',
            'exercises.*.exercise_id' => 'required|integer',
            'exercises.*.status' => 'required|in:completed,partial,not_done',
            'exercises.*.athlete_comment' => 'nullable|string',
            'exercises.*.sets_data' => 'nullable|array'
        ]);
        
        $trainer = auth()->user();
        $workout = Workout::findOrFail($request->workout_id);
        
        // Проверяем, что тренер имеет доступ к этой тренировке
        if ($workout->trainer_id !== $trainer->id) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }
        
        $athlete = $workout->athlete;
        
        foreach ($request->exercises as $exerciseData) {
            \App\Models\Athlete\ExerciseProgress::updateOrCreate(
                [
                    'workout_id' => $request->workout_id,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'athlete_id' => $athlete->id
                ],
                [
                    'status' => $exerciseData['status'] ?? 'not_done',
                    'athlete_comment' => $exerciseData['athlete_comment'] ?? null,
                    'sets_data' => $exerciseData['sets_data'] ?? null,
                    'completed_at' => ($exerciseData['status'] ?? 'not_done') === 'completed' ? now() : null
                ]
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Прогресс обновлен'
        ]);
    }

    /**
     * Сохранение платежа
     */
    public function savePayment(Request $request, $athleteId)
    {
        $request->validate([
            'package_type' => 'required|string',
            'total_sessions' => 'required|integer|min:1',
            'package_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'purchase_date' => 'required|date',
            'payment_description' => 'nullable|string',
            'expires_date' => 'nullable|date',
        ]);

        $athlete = Athlete::where('id', $athleteId)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();

        // Находим или создаем запись в trainer_finances
        $finance = \App\Models\TrainerFinance::updateOrCreate(
            [
                'trainer_id' => auth()->id(),
                'athlete_id' => $athlete->id,
            ],
            [
                'package_type' => $request->package_type,
                'total_sessions' => $request->total_sessions,
                'package_price' => $request->package_price,
                'payment_method' => $request->payment_method,
                'purchase_date' => $request->purchase_date,
                'payment_description' => $request->payment_description,
                'expires_date' => $request->expires_date,
                'total_paid' => $request->package_price,
                'last_payment_date' => $request->purchase_date,
            ]
        );

        // Добавляем новый платеж в историю
        $paymentHistory = $finance->payment_history ?? [];
        $paymentHistory[] = [
            'id' => time() . rand(1000, 9999),
            'date' => $request->purchase_date,
            'amount' => $request->package_price,
            'description' => $request->payment_description ?? $request->package_type,
            'payment_method' => $request->payment_method,
        ];

        $finance->update([
            'payment_history' => $paymentHistory,
            'total_paid' => collect($paymentHistory)->sum('amount'),
            'last_payment_date' => $request->purchase_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Платеж сохранен'
        ]);
    }

    /**
     * Обновление платежа
     */
    public function updatePayment(Request $request, $athleteId, $paymentId)
    {
        $request->validate([
            'package_type' => 'required|string',
            'total_sessions' => 'required|integer|min:1',
            'package_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'purchase_date' => 'required|date',
            'payment_description' => 'nullable|string',
            'expires_date' => 'nullable|date',
        ]);

        $athlete = Athlete::where('id', $athleteId)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();

        $finance = \App\Models\TrainerFinance::where('trainer_id', auth()->id())
            ->where('athlete_id', $athlete->id)
            ->firstOrFail();

        // Обновляем основной платеж
        $finance->update([
            'package_type' => $request->package_type,
            'total_sessions' => $request->total_sessions,
            'package_price' => $request->package_price,
            'payment_method' => $request->payment_method,
            'purchase_date' => $request->purchase_date,
            'payment_description' => $request->payment_description,
            'expires_date' => $request->expires_date,
        ]);

        // Обновляем конкретный платеж в истории
        $paymentHistory = $finance->payment_history ?? [];
        foreach ($paymentHistory as $key => $payment) {
            if ($payment['id'] == $paymentId) {
                $paymentHistory[$key] = [
                    'id' => $paymentId,
                    'date' => $request->purchase_date,
                    'amount' => $request->package_price,
                    'description' => $request->payment_description ?? $request->package_type,
                    'payment_method' => $request->payment_method,
                ];
                break;
            }
        }

        $finance->update([
            'payment_history' => $paymentHistory,
            'total_paid' => collect($paymentHistory)->sum('amount'),
            'last_payment_date' => collect($paymentHistory)->max('date'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Платеж обновлен'
        ]);
    }

    /**
     * Удаление платежа
     */
    public function deletePayment($athleteId, $paymentId)
    {
        $athlete = Athlete::where('id', $athleteId)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();

        $finance = \App\Models\TrainerFinance::where('trainer_id', auth()->id())
            ->where('athlete_id', $athlete->id)
            ->firstOrFail();

        // Удаляем платеж из истории
        $paymentHistory = $finance->payment_history ?? [];
        $paymentHistory = array_filter($paymentHistory, function($payment) use ($paymentId) {
            return $payment['id'] != $paymentId;
        });

        $paymentHistory = array_values($paymentHistory); // Переиндексируем массив

        $finance->update([
            'payment_history' => $paymentHistory,
            'total_paid' => collect($paymentHistory)->sum('amount'),
            'last_payment_date' => collect($paymentHistory)->max('date'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Платеж удален'
        ]);
    }
}
