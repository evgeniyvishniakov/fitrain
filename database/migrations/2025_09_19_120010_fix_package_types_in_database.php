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
        // Обновляем существующие данные в базе
        $packageTypeMapping = [
            'single' => 'Разовая тренировка',
            '4_sessions' => '4 тренировки',
            '8_sessions' => '8 тренировок',
            '12_sessions' => '12 тренировок',
            'unlimited' => 'Безлимит (месяц)',
            'custom' => 'Произвольное количество'
        ];

        foreach ($packageTypeMapping as $oldType => $newType) {
            DB::table('users')
                ->where('package_type', $oldType)
                ->update(['package_type' => $newType]);
        }

        // Обновляем данные в payment_history
        $users = DB::table('users')->whereNotNull('payment_history')->get();
        
        foreach ($users as $user) {
            $paymentHistory = json_decode($user->payment_history, true);
            
            if ($paymentHistory && is_array($paymentHistory)) {
                $updated = false;
                
                foreach ($paymentHistory as &$payment) {
                    if (isset($payment['description'])) {
                        $oldDescription = $payment['description'];
                        
                        // Заменяем коды в описании
                        $payment['description'] = str_replace('4_sessions', '4 тренировки', $payment['description']);
                        $payment['description'] = str_replace('8_sessions', '8 тренировок', $payment['description']);
                        $payment['description'] = str_replace('12_sessions', '12 тренировок', $payment['description']);
                        $payment['description'] = str_replace('single', 'Разовая тренировка', $payment['description']);
                        $payment['description'] = str_replace('unlimited', 'Безлимит (месяц)', $payment['description']);
                        $payment['description'] = str_replace('custom', 'Произвольное количество', $payment['description']);
                        
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
        // Обратное преобразование
        $packageTypeMapping = [
            'Разовая тренировка' => 'single',
            '4 тренировки' => '4_sessions',
            '8 тренировок' => '8_sessions',
            '12 тренировок' => '12_sessions',
            'Безлимит (месяц)' => 'unlimited',
            'Произвольное количество' => 'custom'
        ];

        foreach ($packageTypeMapping as $newType => $oldType) {
            DB::table('users')
                ->where('package_type', $newType)
                ->update(['package_type' => $oldType]);
        }
    }
};