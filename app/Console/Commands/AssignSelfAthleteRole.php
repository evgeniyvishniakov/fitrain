<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shared\User;
use Spatie\Permission\Models\Role;

class AssignSelfAthleteRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-self-athlete {user_id : ID пользователя для назначения роли}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Назначает роль Self-Athlete пользователю';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        // Находим пользователя
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден.");
            return 1;
        }
        
        // Проверяем, что роль существует
        $role = Role::where('name', 'self-athlete')->first();
        
        if (!$role) {
            $this->error("Роль 'self-athlete' не найдена. Запустите: php artisan db:seed --class=RolePermissionSeeder");
            return 1;
        }
        
        // Назначаем роль
        $user->assignRole('self-athlete');
        
        // Убираем тренера, если он есть (Self-Athlete работает самостоятельно)
        $user->update(['trainer_id' => null]);
        
        $this->info("Роль 'self-athlete' успешно назначена пользователю {$user->name} (ID: {$user->id})");
        $this->info("Тренер удален - пользователь теперь работает самостоятельно.");
        
        return 0;
    }
}