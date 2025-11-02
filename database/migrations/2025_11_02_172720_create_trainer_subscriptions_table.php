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
        Schema::create('trainer_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trainer_id'); // ID тренера
            $table->unsignedBigInteger('subscription_plan_id')->nullable(); // ID плана подписки
            $table->enum('status', ['active', 'expired', 'cancelled', 'trial'])->default('trial'); // Статус подписки
            $table->decimal('price', 10, 2)->default(0); // Цена
            $table->string('currency_code', 3)->default('UAH'); // Валюта
            $table->date('start_date'); // Дата начала
            $table->date('expires_date'); // Дата окончания
            $table->boolean('is_trial')->default(true); // Пробный период
            $table->integer('trial_days')->default(7); // Дней пробного периода
            $table->text('notes')->nullable(); // Заметки
            $table->timestamps();
            
            // Индексы
            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null');
            $table->foreign('currency_code')->references('code')->on('currencies')->onDelete('restrict');
            $table->index(['trainer_id', 'status']);
            $table->index('expires_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_subscriptions');
    }
};
