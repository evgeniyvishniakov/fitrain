<?php

namespace App\Http\Controllers\Crm\SelfAthlete;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Workout;
use App\Models\Trainer\Exercise;
use Illuminate\Http\Request;

class SelfAthleteController extends BaseController
{
    /**
     * Дашборд Self-Athlete
     */
    public function dashboard()
    {
        $athlete = auth()->user();
        
        // Общее количество тренировок
        $totalWorkouts = $athlete->workouts()->count();
        
        // Запланированные тренировки
        $plannedWorkouts = $athlete->workouts()->where('status', 'planned')->count();
        
        // Завершенные тренировки
        $completedWorkouts = $athlete->workouts()->where('status', 'completed')->count();
        
        // Последняя тренировка или следующая
        $lastOrNextWorkout = $athlete->workouts()
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->first();
        
        // Ближайшие тренировки (следующие 7 дней)
        $upcomingWorkouts = $athlete->workouts()
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();
        
        // Все тренировки для календаря (расширенный диапазон)
        $monthWorkouts = $athlete->workouts()
            ->where('date', '>=', now()->startOfMonth()->subMonth()->toDateString())
            ->where('date', '<=', now()->endOfMonth()->addMonth()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get()
            ->map(function($workout) {
                return [
                    'id' => $workout->id,
                    'title' => $workout->title,
                    'date' => $workout->date ? $workout->date->format('Y-m-d') : null,
                    'time' => $workout->time,
                    'status' => $workout->status,
                ];
            }) ?? collect();
        
        $recentWorkouts = $athlete->workouts()->latest()->take(5)->get();
        
        // Данные для карточек измерений
        $lastMeasurement = $athlete->measurements()->latest('measurement_date')->first();
        $currentWeight = $lastMeasurement ? $lastMeasurement->weight : $athlete->current_weight;
        $totalMeasurements = $athlete->measurements()->count();
        
        // Рассчитываем ИМТ из последнего измерения
        $bmi = null;
        $bmiCategory = null;
        $bmiColor = null;
        
        if ($lastMeasurement && $lastMeasurement->weight && $athlete->current_height) {
            $heightInMeters = $athlete->current_height / 100;
            $bmi = round($lastMeasurement->weight / ($heightInMeters * $heightInMeters), 1);
        } elseif ($athlete->current_weight && $athlete->current_height) {
            $heightInMeters = $athlete->current_height / 100;
            $bmi = round($athlete->current_weight / ($heightInMeters * $heightInMeters), 1);
        }
        
        // Определяем категорию и цвет ИМТ
        if ($bmi) {
            if ($bmi < 18.5) {
                $bmiCategory = 'Недостаточный вес';
                $bmiColor = 'blue';
            } elseif ($bmi < 25) {
                $bmiCategory = 'Нормальный вес';
                $bmiColor = 'green';
            } elseif ($bmi < 30) {
                $bmiCategory = 'Избыточный вес';
                $bmiColor = 'yellow';
            } else {
                $bmiCategory = 'Ожирение';
                $bmiColor = 'red';
            }
        }
        
        return view('crm.self-athlete.dashboard', compact(
            'athlete', 
            'totalWorkouts', 
            'plannedWorkouts', 
            'completedWorkouts', 
            'lastOrNextWorkout', 
            'upcomingWorkouts', 
            'monthWorkouts', 
            'recentWorkouts', 
            'lastMeasurement', 
            'currentWeight', 
            'totalMeasurements', 
            'bmi', 
            'bmiCategory', 
            'bmiColor'
        ));
    }
    
    // Профиль перенесен в настройки
}
