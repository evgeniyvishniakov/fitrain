<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Trainer\Workout;
use App\Models\Shared\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user()->load('roles');
        
        // Получаем текущую дату или дату из запроса
        $currentDate = $request->get('date', now()->format('Y-m-d'));
        $date = Carbon::parse($currentDate);
        
        // Получаем тренировки для тренера
        if ($user->hasRole('trainer')) {
            // Тренер видит все тренировки (пока что все, потом можно добавить фильтрацию по trainer_id)
            $workouts = Workout::with(['athlete', 'exercises'])
                ->whereBetween('date', [
                    $date->copy()->startOfMonth()->format('Y-m-d'),
                    $date->copy()->endOfMonth()->format('Y-m-d')
                ])
                ->orderBy('date')
                ->orderBy('time')
                ->get();
        } else {
            // Спортсмен видит только свои тренировки
            $workouts = Workout::with(['athlete', 'exercises'])
                ->where('athlete_id', $user->id)
                ->whereBetween('date', [
                    $date->copy()->startOfMonth()->format('Y-m-d'),
                    $date->copy()->endOfMonth()->format('Y-m-d')
                ])
                ->orderBy('date')
                ->orderBy('time')
                ->get();
        }
        
        // Группируем тренировки по дням и добавляем status_color
        $workoutsByDay = $workouts->map(function($workout) {
            $workout->status_color = $this->getStatusColor($workout->status);
            $workout->athlete_name = $workout->athlete->name ?? 'Неизвестно';
            $workout->exercises_count = $workout->exercises->count();
            return $workout;
        })->groupBy(function($workout) {
            return $workout->date;
        });
        
        // Получаем спортсменов для фильтрации (только для тренера)
        $athletes = collect();
        if ($user->hasRole('trainer')) {
            $athletes = User::where('trainer_id', $user->id)
                ->orderBy('name')
                ->get();
        }
        
        return view('crm.trainer.calendar.index', compact(
            'workoutsByDay',
            'date',
            'athletes',
            'currentDate'
        ));
    }
    
    public function getWorkoutsForDate(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $athleteId = $request->get('athlete_id');
        
        $user = auth()->user()->load('roles');
        
        $query = Workout::with(['athlete', 'exercises'])
            ->whereBetween('date', [$startDate, $endDate]);
            
        if ($user->hasRole('trainer')) {
            // Тренер видит все тренировки (пока что все, потом можно добавить фильтрацию по trainer_id)
            if ($athleteId) {
                $query->where('athlete_id', $athleteId);
            }
        } else {
            $query->where('athlete_id', $user->id);
        }
        
        $workouts = $query->orderBy('date')->orderBy('time')->get();
        
        return response()->json([
            'success' => true,
            'workouts' => $workouts->map(function($workout) {
                return [
                    'id' => $workout->id,
                    'title' => $workout->title,
                    'description' => $workout->description,
                    'date' => $workout->date,
                    'time' => $workout->time,
                    'duration' => $workout->duration,
                    'status' => $workout->status,
                    'athlete_name' => $workout->athlete->name ?? 'Неизвестно',
                    'exercises_count' => $workout->exercises->count(),
                    'status_color' => $this->getStatusColor($workout->status),
                ];
            })
        ]);
    }
    
    private function getStatusColor($status)
    {
        return match($status) {
            'completed' => 'green',
            'planned' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}
