<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Общие данные
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
            }
            if (!Schema::hasColumn('users', 'sport_level')) {
                $table->string('sport_level')->nullable(); // новичок, любитель, профи
            }
            if (!Schema::hasColumn('users', 'goals')) {
                $table->json('goals')->nullable(); // цели спортсмена
            }
            if (!Schema::hasColumn('users', 'contact_info')) {
                $table->json('contact_info')->nullable(); // дополнительные контакты
            }
            
            // Медицинские данные
            if (!Schema::hasColumn('users', 'current_weight')) {
                $table->decimal('current_weight', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'current_height')) {
                $table->decimal('current_height', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'health_restrictions')) {
                $table->json('health_restrictions')->nullable(); // ограничения по здоровью
            }
            if (!Schema::hasColumn('users', 'medical_documents')) {
                $table->json('medical_documents')->nullable(); // медицинские справки
            }
            if (!Schema::hasColumn('users', 'last_medical_checkup')) {
                $table->date('last_medical_checkup')->nullable();
            }
            
            // Настройки профиля
            if (!Schema::hasColumn('users', 'profile_modules')) {
                $table->json('profile_modules')->nullable(); // какие модули включены
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'birth_date',
                'gender',
                'sport_level',
                'goals',
                'contact_info',
                'current_weight',
                'current_height',
                'health_restrictions',
                'medical_documents',
                'last_medical_checkup',
                'profile_modules',
                'is_active'
            ]);
        });
    }
};
