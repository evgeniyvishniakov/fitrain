<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Исправляем описания в payment_history
        $users = DB::table('users')->whereNotNull('payment_history')->get();
        
        foreach ($users as $user) {
            $paymentHistory = json_decode($user->payment_history, true);
            
            if ($paymentHistory && is_array($paymentHistory)) {
                $updated = false;
                
                foreach ($paymentHistory as &$payment) {
                    if (isset($payment['description'])) {
                        $oldDescription = $payment['description'];
                        
                        // Исправляем "4 тренирки" на "4 тренировки"
                        $payment['description'] = str_replace('4 тренирки', '4 тренировки', $payment['description']);
                        $payment['description'] = str_replace('8 тренирки', '8 тренировки', $payment['description']);
                        $payment['description'] = str_replace('12 тренирки', '12 тренировки', $payment['description']);
                        
                        if ($oldDescription !== $payment['description']) {
                            $updated = true;
                        }
                    }
                }
                
                if ($updated) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['payment_history' => json_encode($paymentHistory, JSON_UNESCAPED_UNICODE)]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Обратное преобразование
        $users = DB::table('users')->whereNotNull('payment_history')->get();
        
        foreach ($users as $user) {
            $paymentHistory = json_decode($user->payment_history, true);
            
            if ($paymentHistory && is_array($paymentHistory)) {
                $updated = false;
                
                foreach ($paymentHistory as &$payment) {
                    if (isset($payment['description'])) {
                        $oldDescription = $payment['description'];
                        
                        // Обратное преобразование
                        $payment['description'] = str_replace('4 тренировки', '4 тренирки', $payment['description']);
                        $payment['description'] = str_replace('8 тренировки', '8 тренирки', $payment['description']);
                        $payment['description'] = str_replace('12 тренировки', '12 тренирки', $payment['description']);
                        
                        if ($oldDescription !== $payment['description']) {
                            $updated = true;
                        }
                    }
                }
                
                if ($updated) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['payment_history' => json_encode($paymentHistory, JSON_UNESCAPED_UNICODE)]);
                }
            }
        }
    }
};