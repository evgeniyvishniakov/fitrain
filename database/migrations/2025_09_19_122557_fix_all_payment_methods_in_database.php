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
        $paymentMethodLabels = [
            'cash' => 'Наличные',
            'card' => 'Карта',
            'transfer' => 'Перевод',
            'other' => 'Другое'
        ];

        \App\Models\Shared\User::whereNotNull('payment_history')->chunkById(100, function ($users) use ($paymentMethodLabels) {
            foreach ($users as $user) {
                $updated = false;
                
                // Обновляем payment_method в основном поле
                if (isset($paymentMethodLabels[$user->payment_method])) {
                    $user->payment_method = $paymentMethodLabels[$user->payment_method];
                    $updated = true;
                }
                
                // Обновляем payment_history
                if ($user->payment_history) {
                    $paymentHistory = $user->payment_history;
                    foreach ($paymentHistory as &$payment) {
                        if (isset($payment['payment_method']) && isset($paymentMethodLabels[$payment['payment_method']])) {
                            $payment['payment_method'] = $paymentMethodLabels[$payment['payment_method']];
                            $updated = true;
                        }
                    }
                    $user->payment_history = $paymentHistory;
                }
                
                if ($updated) {
                    $user->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Не нужно откатывать - это исправление данных
    }
};