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
            // Удаляем все финансовые поля из таблицы users
            $table->dropColumn([
                'package_type',
                'total_sessions',
                'used_sessions',
                'package_price',
                'purchase_date',
                'expires_date',
                'payment_method',
                'payment_description',
                'payment_history',
                'total_paid',
                'last_payment_date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Восстанавливаем финансовые поля (на случай отката)
            $table->string('package_type')->nullable();
            $table->integer('total_sessions')->default(0);
            $table->integer('used_sessions')->default(0);
            $table->decimal('package_price', 10, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->date('expires_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payment_description')->nullable();
            $table->json('payment_history')->nullable();
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->date('last_payment_date')->nullable();
        });
    }
};