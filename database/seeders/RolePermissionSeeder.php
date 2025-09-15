<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Создаем разрешения
        $permissions = [
            'view_dashboard',
            'view_calendar',
            'view_workouts',
            'create_workouts',
            'edit_workouts',
            'delete_workouts',
            'view_progress',
            'edit_progress',
            'view_nutrition',
            'edit_nutrition',
            'manage_clients', // только для тренеров
            'view_settings',
            'edit_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Создаем роли
        $trainerRole = Role::firstOrCreate(['name' => 'trainer']);
        $athleteRole = Role::firstOrCreate(['name' => 'athlete']);

        // Назначаем разрешения тренеру
        $trainerRole->syncPermissions([
            'view_dashboard',
            'view_calendar',
            'view_workouts',
            'create_workouts',
            'edit_workouts',
            'delete_workouts',
            'view_progress',
            'edit_progress',
            'view_nutrition',
            'edit_nutrition',
            'manage_clients',
            'view_settings',
            'edit_settings',
        ]);

        // Назначаем разрешения спортсмену
        $athleteRole->syncPermissions([
            'view_dashboard',
            'view_calendar',
            'view_workouts',
            'view_progress',
            'edit_progress',
            'view_nutrition',
            'edit_nutrition',
            'view_settings',
            'edit_settings',
        ]);
    }
}
