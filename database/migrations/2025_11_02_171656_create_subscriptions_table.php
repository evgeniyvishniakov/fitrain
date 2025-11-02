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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название подписки
            $table->text('description')->nullable(); // Описание
            $table->decimal('price', 10, 2)->default(0); // Цена
            $table->string('currency_code', 3)->default('UAH'); // Валюта
            $table->boolean('is_active')->default(true); // Активна ли подписка
            $table->timestamps();
            
            $table->foreign('currency_code')->references('code')->on('currencies')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
