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
        
        // Проверяем разные форматы описаний
        // Универсальное регулярное выражение для всех вариантов написания
        
        // Основной вариант: "4 тренировки", "8 тренировок", "12 тренировок"
        if (preg_match('/(\d+)\s*тренировок?/ui', $description, $matches)) {
            $sessions = (int)$matches[1];
            \Log::info("Extracted {$sessions} sessions from: '{$description}' (тренировок)");
            return $sessions;
        }
        
        // Вариант с "тренирки": "4 тренирки", "8 тренирки"
        if (preg_match('/(\d+)\s*тренирки/ui', $description, $matches)) {
            $sessions = (int)$matches[1];
            \Log::info("Extracted {$sessions} sessions from: '{$description}' (тренирки)");
            return $sessions;
        }
        
        // Разовая тренировка
        if (preg_match('/Разовая\s*тренировка/ui', $description)) {
            \Log::info("Extracted 1 session from: '{$description}' (single)");
            return 1;
        }
        
        // Безлимит
        if (preg_match('/Безлимит/ui', $description)) {
            \Log::info("Extracted 30 sessions (unlimited) from: '{$description}'");
            return 30;
        }
        
        \Log::info("No sessions extracted from: '{$description}'");
        return 0;
    }
    
    public function dashboard()
    {
        $trainer = auth()->user();
        $athletes = $trainer->athletes()->count();
        $workouts = $trainer->trainerWorkouts()->count();
        $recentWorkouts = $trainer->trainerWorkouts()->with('athlete')->latest()->take(5)->get();
        
        return view('crm.trainer.dashboard', compact('trainer', 'athletes', 'workouts', 'recentWorkouts'));
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
            $query->latest()->take(1);
        }])->paginate(12);
        
        // Добавляем финансовые данные для каждого спортсмена
        $athletes->getCollection()->transform(function ($athlete) {
            // Суммируем все пакеты из истории платежей
            $totalSessionsFromHistory = 0;
            $totalPaidFromHistory = 0;
            $paymentHistory = $athlete->payment_history ?? [];
            
            foreach ($paymentHistory as $payment) {
                // Извлекаем количество тренировок из описания
                $description = $payment['description'] ?? '';
                
                // Отладочная информация
                \Log::info("Raw description from DB: " . json_encode($description, JSON_UNESCAPED_UNICODE));
                \Log::info("Description length: " . strlen($description));
                \Log::info("Description bytes: " . bin2hex($description));
                
                $sessions = $this->extractSessionsFromDescription($description);
                $totalSessionsFromHistory += $sessions;
                
                // Суммируем все платежи
                $totalPaidFromHistory += $payment['amount'] ?? 0;
            }
            
            // Если нет истории платежей, используем данные из полей пользователя
            if (empty($paymentHistory)) {
                $totalSessionsFromHistory = $athlete->total_sessions;
                $totalPaidFromHistory = $athlete->total_paid;
            }
            
            // Определяем тип пакета для отображения
            $packageTypeDisplay = $athlete->package_type;
            if ($totalSessionsFromHistory > 0) {
                if ($totalSessionsFromHistory == 1) {
                    $packageTypeDisplay = 'Разовая тренировка';
                } elseif ($totalSessionsFromHistory >= 30) {
                    $packageTypeDisplay = 'Безлимит (месяц)';
                } else {
                    $packageTypeDisplay = $totalSessionsFromHistory . ' тренировок';
                }
            }
            
            // Отладочная информация
            \Log::info('=== ATHLETE DEBUG INFO ===');
            \Log::info('Athlete ID: ' . $athlete->id);
            \Log::info('Athlete Name: ' . $athlete->name);
            \Log::info('Payment History: ' . json_encode($paymentHistory));
            \Log::info('Total Sessions From History: ' . $totalSessionsFromHistory);
            \Log::info('Used Sessions: ' . $athlete->used_sessions);
            \Log::info('Remaining Sessions: ' . (($totalSessionsFromHistory ?: $athlete->total_sessions) - $athlete->used_sessions));
            \Log::info('Package Type Display: ' . $packageTypeDisplay);
            \Log::info('========================');
            
            $athlete->finance = [
                'id' => $athlete->id,
                'package_type' => $packageTypeDisplay,
                'total_sessions' => $totalSessionsFromHistory ?: $athlete->total_sessions,
                'used_sessions' => $athlete->used_sessions,
                'remaining_sessions' => ($totalSessionsFromHistory ?: $athlete->total_sessions) - $athlete->used_sessions,
                'package_price' => $totalPaidFromHistory ?: $athlete->package_price,
                'purchase_date' => $athlete->purchase_date,
                'expires_date' => $athlete->expires_date,
                'status' => ($totalSessionsFromHistory ?: $athlete->total_sessions) > 0 ? 'active' : 'inactive',
                'total_paid' => $totalPaidFromHistory ?: $athlete->total_paid,
                'last_payment_date' => $athlete->last_payment_date,
                'payment_history' => $paymentHistory
            ];
            
            
            return $athlete;
        });
        
        return view('crm.trainer.athletes.index', compact('athletes'));
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
                'sport_level' => 'nullable|in:beginner,intermediate,advanced',
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
                'sport_level' => $request->sport_level,
                'goals' => $request->goals ? json_encode($request->goals) : null,
                'health_restrictions' => $request->health_restrictions ? json_encode([['type' => 'Общие ограничения', 'description' => $request->health_restrictions]]) : null,
                'is_active' => $request->is_active ?? true,
                'trainer_id' => auth()->id(),
            ]);
            
            // Назначаем роль спортсмену
            try {
                $athleteRole = \Spatie\Permission\Models\Role::where('name', 'athlete')->first();
                if ($athleteRole) {
                    $athlete->assignRole($athleteRole);
                }
            } catch (\Exception $e) {
                // Если не удалось назначить роль, продолжаем без неё
                \Log::warning('Не удалось назначить роль athlete: ' . $e->getMessage());
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
        ]);
        
        $updateData = $request->all();
        
        // Если пароль указан, хешируем его
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        } else {
            // Убираем пароль из данных обновления, если он не указан
            unset($updateData['password']);
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
                'updated_at' => $athlete->updated_at
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
        \Log::info('TrainerController::getExerciseProgress called', [
            'workout_id' => $request->get('workout_id'),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->roles?->first()?->name
        ]);
        
        $workoutId = $request->get('workout_id');
        
        if (!$workoutId) {
            return response()->json(['error' => 'workout_id is required'], 400);
        }
        
        // Получаем прогресс упражнений для указанной тренировки
        $progress = \App\Models\Athlete\ExerciseProgress::where('workout_id', $workoutId)->get();
        
        \Log::info('Exercise progress found', [
            'workout_id' => $workoutId,
            'count' => $progress->count(),
            'data' => $progress->toArray()
        ]);
        
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
}
