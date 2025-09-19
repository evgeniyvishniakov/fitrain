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
            // Финансовые поля для пакетов тренировок
            $table->string('package_type')->nullable()->after('is_active');
            $table->integer('total_sessions')->default(0)->after('package_type');
            $table->integer('used_sessions')->default(0)->after('total_sessions');
            $table->decimal('package_price', 10, 2)->default(0)->after('used_sessions');
            $table->date('purchase_date')->nullable()->after('package_price');
            $table->date('expires_date')->nullable()->after('purchase_date');
            $table->string('payment_method')->nullable()->after('expires_date');
            $table->text('payment_description')->nullable()->after('payment_method');
            $table->json('payment_history')->nullable()->after('payment_description');
            $table->decimal('total_paid', 10, 2)->default(0)->after('payment_history');
            $table->date('last_payment_date')->nullable()->after('total_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
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
};