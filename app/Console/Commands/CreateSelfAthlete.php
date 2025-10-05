<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shared\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateSelfAthlete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-self-athlete 
                            {name : Имя пользователя}
                            {email : Email пользователя}
                            {password : Пароль пользователя}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создает нового пользователя с ролью Self-Athlete';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        // Проверяем, что пользователь с таким email не существует
        if (User::where('email', $email)->exists()) {
            $this->error("Пользователь с email {$email} уже существует.");
            return 1;
        }
        
        // Проверяем, что роль существует
        $role = Role::where('name', 'self-athlete')->first();
        
        if (!$role) {
            $this->error("Роль 'self-athlete' не найдена. Запустите: php artisan db:seed --class=RolePermissionSeeder");
            return 1;
        }
        
        // Создаем пользователя
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'trainer_id' => null, // Self-Athlete работает самостоятельно
            'is_active' => true,
        ]);
        
        // Назначаем роль
        $user->assignRole('self-athlete');
        
        $this->info("✅ Self-Athlete успешно создан!");
        $this->info("ID: {$user->id}");
        $this->info("Имя: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Роль: self-athlete");
        $this->info("Тренер: нет (работает самостоятельно)");
        
        $this->line("");
        $this->info("Возможности Self-Athlete:");
        $this->line("✅ Создавать тренировки");
        $this->line("✅ Создавать упражнения");
        $this->line("✅ Управлять своим прогрессом");
        $this->line("✅ Просматривать календарь");
        $this->line("❌ Создавать других спортсменов");
        
        return 0;
    }
}