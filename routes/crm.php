<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\Auth\LoginController;
use App\Http\Controllers\Crm\Auth\RegisterController;
use App\Http\Controllers\Crm\TrainerController;
use App\Http\Controllers\Crm\AthleteController;
use App\Http\Controllers\Crm\WorkoutController;
use App\Http\Controllers\Crm\ProgressController;
use App\Http\Controllers\Crm\NutritionController;
use App\Http\Controllers\Crm\ExerciseController;

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
        Route::get("/trainer/athletes/add", [TrainerController::class, "addAthlete"])->name("crm.trainer.add-athlete");
        Route::post("/trainer/athletes", [TrainerController::class, "storeAthlete"])->name("crm.trainer.store-athlete");
        Route::delete("/trainer/athletes/{id}", [TrainerController::class, "removeAthlete"])->name("crm.trainer.remove-athlete");
    });
    
    // Маршруты только для спортсменов
    Route::middleware(["role:athlete"])->group(function () {
        Route::get("/athlete/dashboard", [AthleteController::class, "dashboard"])->name("crm.athlete.dashboard");
        Route::get("/athlete/profile", [AthleteController::class, "profile"])->name("crm.athlete.profile");
        Route::post("/athlete/profile", [AthleteController::class, "updateProfile"])->name("crm.athlete.profile.update");
        Route::get("/athlete/workouts", [AthleteController::class, "workouts"])->name("crm.athlete.workouts");
        Route::get("/athlete/progress", [AthleteController::class, "progress"])->name("crm.athlete.progress");
        Route::get("/athlete/nutrition", [AthleteController::class, "nutrition"])->name("crm.athlete.nutrition");
    });
    
    // Маршруты для всех авторизованных
    Route::get("/workouts", [WorkoutController::class, "index"])->name("crm.workouts.index");
    Route::get("/workouts/create", [WorkoutController::class, "create"])->name("crm.workouts.create");
    Route::post("/workouts", [WorkoutController::class, "store"])->name("crm.workouts.store");
    Route::get("/workouts/{id}", [WorkoutController::class, "show"])->name("crm.workouts.show");
    Route::get("/workouts/{id}/edit", [WorkoutController::class, "edit"])->name("crm.workouts.edit");
    Route::put("/workouts/{id}", [WorkoutController::class, "update"])->name("crm.workouts.update");
    Route::delete("/workouts/{id}", [WorkoutController::class, "destroy"])->name("crm.workouts.destroy");
    
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
    
    // Каталог упражнений
    Route::get("/exercises", [ExerciseController::class, "index"])->name("crm.exercises.index");
    Route::get("/exercises/create", [ExerciseController::class, "create"])->name("crm.exercises.create");
    Route::post("/exercises", [ExerciseController::class, "store"])->name("crm.exercises.store");
    Route::get("/exercises/{id}", [ExerciseController::class, "show"])->name("crm.exercises.show");
    Route::get("/exercises/{id}/edit", [ExerciseController::class, "edit"])->name("crm.exercises.edit");
    Route::put("/exercises/{id}", [ExerciseController::class, "update"])->name("crm.exercises.update");
    Route::delete("/exercises/{id}", [ExerciseController::class, "destroy"])->name("crm.exercises.destroy");
    
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
    
    Route::get("/calendar", function () {
        return "Календарь";
    })->name("crm.calendar");
    
    Route::get("/settings", function () {
        return "Настройки";
    })->name("crm.settings");
});
