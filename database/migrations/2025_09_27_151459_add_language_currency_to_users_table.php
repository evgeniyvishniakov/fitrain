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
            $table->string('language_code', 5)->default('ru')->after('is_active');
            $table->string('currency_code', 3)->default('RUB')->after('language_code');
            $table->string('timezone')->default('Europe/Moscow')->after('currency_code');
            
            // Индексы для быстрого поиска
            $table->index('language_code');
            $table->index('currency_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['language_code']);
            $table->dropIndex(['currency_code']);
            $table->dropColumn(['language_code', 'currency_code', 'timezone']);
        });
    }
};