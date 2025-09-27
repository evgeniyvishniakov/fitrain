<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shared\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем роли если их нет
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $trainerRole = Role::firstOrCreate(['name' => 'trainer']);
        $athleteRole = Role::firstOrCreate(['name' => 'athlete']);

        // Создаем базовые права
        $permissions = [
            'manage-users',
            'manage-system',
            'view-statistics',
            'manage-exercises',
            'manage-templates',
            'manage-workouts',
            'view-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Назначаем права роли админа
        $adminRole->syncPermissions($permissions);

        // Создаем админа по умолчанию
        $admin = User::firstOrCreate(
            ['email' => 'admin@fitrain.com'],
            [
                'name' => 'Администратор',
                'password' => Hash::make('admin123'),
                'phone' => '+7 (999) 123-45-67',
                'is_active' => true,
            ]
        );

        // Назначаем роль админа
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Создаем тестового тренера
        $trainer = User::firstOrCreate(
            ['email' => 'trainer@fitrain.com'],
            [
                'name' => 'Иван Тренеров',
                'password' => Hash::make('trainer123'),
                'phone' => '+7 (999) 234-56-78',
                'specialization' => 'Силовые тренировки',
                'experience_years' => 5,
                'is_active' => true,
            ]
        );

        if (!$trainer->hasRole('trainer')) {
            $trainer->assignRole('trainer');
        }

        // Создаем тестового спортсмена
        $athlete = User::firstOrCreate(
            ['email' => 'athlete@fitrain.com'],
            [
                'name' => 'Анна Спортсменова',
                'password' => Hash::make('athlete123'),
                'phone' => '+7 (999) 345-67-89',
                'age' => 25,
                'weight' => 65,
                'height' => 170,
                'gender' => 'female',
                'sport_level' => 'Средний',
                'goals' => ['Похудение', 'Укрепление мышц'],
                'trainer_id' => $trainer->id,
                'is_active' => true,
            ]
        );

        if (!$athlete->hasRole('athlete')) {
            $athlete->assignRole('athlete');
        }

        $this->command->info('Созданы пользователи:');
        $this->command->info('- Админ: admin@fitrain.com / admin123');
        $this->command->info('- Тренер: trainer@fitrain.com / trainer123');
        $this->command->info('- Спортсмен: athlete@fitrain.com / athlete123');
    }
}
