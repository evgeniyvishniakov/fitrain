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
        Schema::create('trainer_finances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trainer_id'); // ID тренера
            $table->unsignedBigInteger('athlete_id'); // ID спортсмена
            $table->string('package_type')->nullable(); // Тип пакета
            $table->integer('total_sessions')->default(0); // Общее количество сессий
            $table->integer('used_sessions')->default(0); // Использованные сессии
            $table->decimal('package_price', 10, 2)->default(0); // Цена пакета
            $table->date('purchase_date')->nullable(); // Дата покупки
            $table->date('expires_date')->nullable(); // Дата истечения
            $table->string('payment_method')->nullable(); // Способ оплаты
            $table->text('payment_description')->nullable(); // Описание платежа
            $table->json('payment_history')->nullable(); // История платежей
            $table->decimal('total_paid', 10, 2)->default(0); // Общая сумма оплачено
            $table->date('last_payment_date')->nullable(); // Дата последнего платежа
            $table->timestamps();
            
            // Индексы
            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('athlete_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['trainer_id', 'athlete_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_finances');
    }
};
