<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Crm\Shared\DashboardController;
use App\Http\Controllers\Crm\Auth\LoginController;
use App\Http\Controllers\Crm\Auth\RegisterController;
use App\Http\Controllers\Crm\Trainer\TrainerController;
use App\Http\Controllers\Crm\Athlete\AthleteController;
use App\Http\Controllers\Crm\Trainer\WorkoutController;
use App\Http\Controllers\Crm\Trainer\ProgressController;
use App\Http\Controllers\Crm\Trainer\NutritionController;
use App\Http\Controllers\Crm\Trainer\ExerciseController;
use App\Http\Controllers\Crm\Trainer\WorkoutTemplateController;
use App\Http\Controllers\Crm\Trainer\CalendarController;
use App\Http\Controllers\Crm\Trainer\SubscriptionController;

/*
|--------------------------------------------------------------------------
| CRM Routes (crm.fitrain.local)
|--------------------------------------------------------------------------
|
| Маршруты для CRM системы - тренеры и клиенты
|
*/

// Главная страница CRM
Route::get("/", [DashboardController::class, "index"])->name("crm.dashboard");

// Маршруты аутентификации
Route::get("/login", [LoginController::class, "showLoginForm"])->name("crm.login");
Route::post("/login", [LoginController::class, "login"]);
Route::post("/logout", [LoginController::class, "logout"])->name("crm.logout");

// Регистрация перенесена на лендинг

// Защищенные маршруты
Route::middleware(["auth"])->group(function () {
    Route::get("/dashboard", [DashboardController::class, "dashboard"])->name("crm.dashboard.main");
    
    // Маршруты только для тренеров
    Route::middleware(["role:trainer"])->group(function () {
        Route::get("/trainer/dashboard", [TrainerController::class, "dashboard"])->name("crm.trainer.dashboard");
        Route::get("/trainer/profile", [TrainerController::class, "profile"])->name("crm.trainer.profile");
        Route::post("/trainer/profile", [TrainerController::class, "updateProfile"])->name("crm.trainer.profile.update");
        Route::get("/trainer/athletes", [TrainerController::class, "athletes"])->name("crm.trainer.athletes");
        Route::get("/trainer/athletes/{id}", [TrainerController::class, "showAthlete"])->name("crm.trainer.athlete.show");
        Route::get("/trainer/athletes/{id}/edit", [TrainerController::class, "editAthlete"])->name("crm.trainer.athlete.edit");
        Route::get("/trainer/athletes/add", [TrainerController::class, "addAthlete"])->name("crm.trainer.add-athlete");
        Route::post("/trainer/athletes", [TrainerController::class, "storeAthlete"])->name("crm.trainer.store-athlete");
        Route::put("/trainer/athletes/{id}", [TrainerController::class, "updateAthlete"])->name("crm.trainer.athlete.update");
        Route::delete("/trainer/athletes/{id}", [TrainerController::class, "removeAthlete"])->name("crm.trainer.remove-athlete");
        
        // Измерения спортсменов
        Route::post("/trainer/athletes/{id}/measurements", [TrainerController::class, "storeMeasurement"])->name("crm.trainer.athlete.measurements.store");
        Route::get("/trainer/athletes/{id}/measurements", [TrainerController::class, "getMeasurements"])->name("crm.trainer.athlete.measurements.get");
        Route::put("/trainer/athletes/{athleteId}/measurements/{measurementId}", [TrainerController::class, "updateMeasurement"])->name("crm.trainer.athlete.measurements.update");
        Route::delete("/trainer/athletes/{athleteId}/measurements/{measurementId}", [TrainerController::class, "deleteMeasurement"])->name("crm.trainer.athlete.measurements.delete");
        
        // Тренировки только для тренеров (управление)
        Route::post("/workouts", [WorkoutController::class, "store"])->name("crm.workouts.store");
        Route::put("/workouts/{id}", [WorkoutController::class, "update"])->name("crm.workouts.update");
        Route::patch("/workouts/{id}/status", [WorkoutController::class, "updateStatus"])->name("crm.workouts.update-status");
        Route::delete("/workouts/{id}", [WorkoutController::class, "destroy"])->name("crm.workouts.destroy");
        
        // Подписка тренера
        Route::get("/trainer/subscription", [SubscriptionController::class, "index"])->name("crm.trainer.subscription");
        
        // Настройки тренера
        Route::get("/trainer/settings", [\App\Http\Controllers\Crm\Trainer\SettingsController::class, "index"])->name("crm.trainer.settings");
        Route::post("/trainer/settings/profile", [\App\Http\Controllers\Crm\Trainer\SettingsController::class, "updateProfile"])->name("crm.trainer.settings.profile");
        Route::post("/trainer/settings/password", [\App\Http\Controllers\Crm\Trainer\SettingsController::class, "updatePassword"])->name("crm.trainer.settings.password");
        Route::post("/trainer/settings/security", [\App\Http\Controllers\Crm\Trainer\SettingsController::class, "updateSecurity"])->name("crm.trainer.settings.security");
        Route::post("/trainer/settings/preferences", [\App\Http\Controllers\Crm\Trainer\SettingsController::class, "updatePreferences"])->name("crm.trainer.settings.preferences");
        Route::post("/trainer/settings/notifications", [\App\Http\Controllers\Crm\Trainer\SettingsController::class, "updateNotifications"])->name("crm.trainer.settings.notifications");
        
        // Каталог упражнений (только для тренеров)
        Route::get("/exercises", [ExerciseController::class, "index"])->name("crm.exercises.index");
        Route::get("/exercises/api", [ExerciseController::class, "api"])->name("crm.exercises.api");
        Route::get("/exercises/create", [ExerciseController::class, "create"])->name("crm.exercises.create");
        Route::post("/exercises", [ExerciseController::class, "store"])->name("crm.exercises.store");
        
        // Пользовательские видео к системным упражнениям (должны быть ДО /exercises/{id})
        Route::get("/exercises/user-videos", [ExerciseController::class, "getAllUserVideos"])->name("crm.exercises.user-videos.all");
        Route::post("/exercises/{id}/user-video", [ExerciseController::class, "storeUserVideo"])->name("crm.exercises.user-video.store");
        Route::get("/exercises/{id}/user-video", [ExerciseController::class, "getUserVideo"])->name("crm.exercises.user-video.get");
        Route::delete("/exercises/{id}/user-video", [ExerciseController::class, "deleteUserVideo"])->name("crm.exercises.user-video.delete");
        
        Route::get("/exercises/{id}", [ExerciseController::class, "show"])->name("crm.exercises.show");
        Route::get("/exercises/{id}/edit", [ExerciseController::class, "edit"])->name("crm.exercises.edit");
        Route::put("/exercises/{id}", [ExerciseController::class, "update"])->name("crm.exercises.update");
        Route::delete("/exercises/{id}", [ExerciseController::class, "destroy"])->name("crm.exercises.destroy");

        // Шаблоны тренировок (только для тренеров)
        Route::get("/workout-templates", [WorkoutTemplateController::class, "index"])->name("crm.workout-templates.index");
        Route::get("/workout-templates/create", [WorkoutTemplateController::class, "create"])->name("crm.workout-templates.create");
        Route::post("/workout-templates", [WorkoutTemplateController::class, "store"])->name("crm.workout-templates.store");
        Route::post("/workout-templates/generate", [WorkoutTemplateController::class, "generate"])->name("crm.workout-templates.generate");
        Route::get("/workout-templates/{id}", [WorkoutTemplateController::class, "show"])->name("crm.workout-templates.show");
        Route::get("/workout-templates/{id}/edit", [WorkoutTemplateController::class, "edit"])->name("crm.workout-templates.edit");
        Route::put("/workout-templates/{id}", [WorkoutTemplateController::class, "update"])->name("crm.workout-templates.update");
        Route::delete("/workout-templates/{id}", [WorkoutTemplateController::class, "destroy"])->name("crm.workout-templates.destroy");
        
        // Прогресс упражнений для тренеров
        Route::get("/trainer/exercise-progress", [TrainerController::class, "getExerciseProgress"])->name("crm.trainer.exercise-progress.get");
        Route::patch("/trainer/exercise-progress", [TrainerController::class, "updateExerciseProgress"])->name("crm.trainer.exercise-progress.update");
        
        // Планы питания для тренеров
        Route::get("/trainer/nutrition-plans", [\App\Http\Controllers\Crm\Trainer\NutritionPlanController::class, "index"])->name("crm.trainer.nutrition-plans.index");
        Route::post("/trainer/nutrition-plans", [\App\Http\Controllers\Crm\Trainer\NutritionPlanController::class, "store"])->name("crm.trainer.nutrition-plans.store");
        Route::get("/trainer/nutrition-plans/{id}", [\App\Http\Controllers\Crm\Trainer\NutritionPlanController::class, "show"])->name("crm.trainer.nutrition-plans.show");
        Route::put("/trainer/nutrition-plans/{id}", [\App\Http\Controllers\Crm\Trainer\NutritionPlanController::class, "update"])->name("crm.trainer.nutrition-plans.update");
        Route::delete("/trainer/nutrition-plans/{id}", [\App\Http\Controllers\Crm\Trainer\NutritionPlanController::class, "destroy"])->name("crm.trainer.nutrition-plans.destroy");
        Route::post("/trainer/nutrition-plans/{id}/days", [\App\Http\Controllers\Crm\Trainer\NutritionPlanController::class, "saveDays"])->name("crm.trainer.nutrition-plans.save-days");
        Route::post("/trainer/nutrition-plans/{id}/day", [\App\Http\Controllers\Crm\Trainer\NutritionPlanController::class, "saveDay"])->name("crm.trainer.nutrition-plans.save-day");
        Route::delete("/trainer/nutrition-plans/{planId}/days/{dayId}", [\App\Http\Controllers\Crm\Trainer\NutritionPlanController::class, "deleteDay"])->name("crm.trainer.nutrition-plans.delete-day");
    });
    
    // Маршруты только для спортсменов
    Route::middleware(["role:athlete"])->group(function () {
        Route::get("/athlete/dashboard", [AthleteController::class, "dashboard"])->name("crm.athlete.dashboard");
        Route::get("/athlete/profile", [AthleteController::class, "profile"])->name("crm.athlete.profile");
        Route::post("/athlete/profile", [AthleteController::class, "updateProfile"])->name("crm.athlete.profile.update");
        Route::put("/athlete/profile", [AthleteController::class, "updateProfile"])->name("crm.athlete.profile.update");
    Route::get("/athlete/workouts", [AthleteController::class, "workouts"])->name("crm.athlete.workouts");
    Route::get("/athlete/workouts/api", [AthleteController::class, "getWorkouts"])->name("crm.athlete.workouts.api");
    Route::get("/athlete/progress", [AthleteController::class, "progress"])->name("crm.athlete.progress");
        Route::get("/athlete/measurements", [AthleteController::class, "measurements"])->name("crm.athlete.measurements");
        Route::post("/athlete/measurements", [AthleteController::class, "storeMeasurement"])->name("crm.athlete.measurements.store");
        Route::get("/athlete/measurements/{id}", [AthleteController::class, "getMeasurement"])->name("crm.athlete.measurements.get");
        Route::put("/athlete/measurements/{id}", [AthleteController::class, "updateMeasurement"])->name("crm.athlete.measurements.update");
        
        // Питание спортсмена
        Route::get("/athlete/nutrition", [\App\Http\Controllers\Crm\Athlete\NutritionController::class, "index"])->name("crm.athlete.nutrition");
        Route::get("/athlete/nutrition-plans", [\App\Http\Controllers\Crm\Athlete\NutritionController::class, "getPlans"])->name("crm.athlete.nutrition-plans");
        Route::delete("/athlete/measurements/{id}", [AthleteController::class, "deleteMeasurement"])->name("crm.athlete.measurements.delete");
        Route::get("/athlete/settings", [AthleteController::class, "settings"])->name("crm.athlete.settings");
        Route::put("/athlete/settings", [AthleteController::class, "updateSettings"])->name("crm.athlete.settings.update");
        
        // Прогресс упражнений
        Route::patch("/athlete/exercise-progress", [AthleteController::class, "updateExerciseProgress"])->name("crm.athlete.exercise-progress.update");
        Route::get("/athlete/exercise-progress", [AthleteController::class, "getExerciseProgress"])->name("crm.athlete.exercise-progress.get");
        
        // Упражнения для спортсмена (только просмотр)
        Route::get("/athlete/exercises", [AthleteController::class, "exercises"])->name("crm.athlete.exercises");
        Route::get("/athlete/exercises/from-workouts", [AthleteController::class, "getExercisesFromWorkouts"])->name("crm.athlete.exercises.from-workouts");
        
        // Обновление статуса тренировки спортсменом
        Route::patch("/athlete/workouts/{workoutId}/status", [AthleteController::class, "updateWorkoutStatus"])->name("crm.athlete.workout.status.update");
        
    });
    
    // Маршруты для всех авторизованных
    
    // Общий маршрут для просмотра тренировок (тренеры видят все, атлеты - свои)
    Route::get("/workouts", function() {
        if (auth()->user()->hasRole('trainer')) {
            return app(\App\Http\Controllers\Crm\Trainer\WorkoutController::class)->index();
        } elseif (auth()->user()->hasRole('athlete')) {
            return app(\App\Http\Controllers\Crm\Athlete\AthleteController::class)->workouts();
        }
        abort(403, 'Доступ запрещен');
    })->name("crm.workouts.index");
    
    // Просмотр конкретной тренировки
    Route::get("/workouts/{id}", function($id) {
        if (auth()->user()->hasRole('trainer')) {
            return app(\App\Http\Controllers\Crm\Trainer\WorkoutController::class)->show($id);
        } elseif (auth()->user()->hasRole('athlete')) {
            // Атлеты могут видеть только свои тренировки
            $workout = \App\Models\Trainer\Workout::where('id', $id)
                ->where('athlete_id', auth()->id())
                ->firstOrFail();
            return view('crm.athlete.workouts.show', compact('workout'));
        }
        abort(403, 'Доступ запрещен');
    })->name("crm.workouts.show");
    
    Route::get("/progress", [ProgressController::class, "index"])->name("crm.progress.index");
    Route::get("/progress/create", [ProgressController::class, "create"])->name("crm.progress.create");
    Route::post("/progress", [ProgressController::class, "store"])->name("crm.progress.store");
    Route::get("/progress/{id}", [ProgressController::class, "show"])->name("crm.progress.show");
    Route::get("/progress/{id}/edit", [ProgressController::class, "edit"])->name("crm.progress.edit");
    Route::put("/progress/{id}", [ProgressController::class, "update"])->name("crm.progress.update");
    
    Route::get("/nutrition", [NutritionController::class, "index"])->name("crm.nutrition.index");
    Route::get("/nutrition/create", [NutritionController::class, "create"])->name("crm.nutrition.create");
    Route::post("/nutrition", [NutritionController::class, "store"])->name("crm.nutrition.store");
    Route::get("/nutrition/{id}/edit", [NutritionController::class, "edit"])->name("crm.nutrition.edit");
    Route::put("/nutrition/{id}", [NutritionController::class, "update"])->name("crm.nutrition.update");
    Route::delete("/nutrition/{id}", [NutritionController::class, "destroy"])->name("crm.nutrition.destroy");
    
    // Общие маршруты для профиля
    Route::get("/profile", function () {
        $user = auth()->user();
        if ($user->hasRole('trainer')) {
            return redirect()->route('crm.trainer.profile');
        } elseif ($user->hasRole('athlete')) {
            return redirect()->route('crm.athlete.profile');
        }
        return redirect()->route('crm.dashboard.main');
    })->name("crm.profile");
    
    Route::get("/calendar", [CalendarController::class, "index"])->name("crm.calendar");
    Route::get("/calendar/workouts", [CalendarController::class, "getWorkoutsForDate"])->name("crm.calendar.workouts");
    
    Route::get("/settings", function () {
        return "Настройки";
    })->name("crm.settings");
});
