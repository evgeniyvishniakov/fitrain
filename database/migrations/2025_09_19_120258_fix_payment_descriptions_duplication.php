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
        // Исправляем дублирование в описаниях платежей
        $users = DB::table('users')->whereNotNull('payment_history')->get();
        
        foreach ($users as $user) {
            $paymentHistory = json_decode($user->payment_history, true);
            
            if ($paymentHistory && is_array($paymentHistory)) {
                $updated = false;
                
                foreach ($paymentHistory as &$payment) {
                    if (isset($payment['description'])) {
                        $oldDescription = $payment['description'];
                        
                        // Исправляем дублирование типа "4 тренировки - 4 тренировок"
                        $payment['description'] = preg_replace('/^(.+?) - \1$/', '$1', $payment['description']);
                        
                        if ($oldDescription !== $payment['description']) {
                            $updated = true;
                        }
                    }
                }
                
                if ($updated) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['payment_history' => json_encode($paymentHistory)]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Обратное преобразование не нужно
    }
};