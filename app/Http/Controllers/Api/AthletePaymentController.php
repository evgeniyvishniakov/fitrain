<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shared\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AthletePaymentController extends Controller
{
    /**
     * Извлекает количество тренировок из описания платежа
     */
    private function extractSessionsFromDescription($description)
    {
        if (empty($description)) {
            return 0;
        }
        
        // Основной вариант: "4 тренировки", "8 тренировок", "12 тренировок"
        // Учитываем все окончания: тренировка, тренировки, тренировок
        if (preg_match('/(\d+)\s+(?:тренировка|тренировки|тренировок)/ui', $description, $matches)) {
            return (int)$matches[1];
        }
        
        // Вариант с "тренирки"
        if (preg_match('/(\d+)\s*тренирки/ui', $description, $matches)) {
            return (int)$matches[1];
        }
        
        // Разовая тренировка
        if (preg_match('/Разовая\s*тренировка/ui', $description)) {
            return 1;
        }
        
        // Безлимит
        if (preg_match('/Безлимит/ui', $description)) {
            return 30;
        }
        
        return 0;
    }
    
    /**
     * Создать платеж для спортсмена
     */
    public function store(Request $request, $athleteId)
    {
        // Проверяем, что запрос ожидает JSON
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Expected JSON request'], 400);
        }

        $validator = Validator::make($request->all(), [
            'package_type' => 'required|string|max:255',
            'total_sessions' => 'required|integer|min:1',
            'used_sessions' => 'integer|min:0',
            'package_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'expires_date' => 'nullable|date',
            'payment_method' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $athlete = User::findOrFail($athleteId);
            
            // Проверяем, что текущий пользователь - тренер этого спортсмена
            if (Auth::user()->id !== $athlete->trainer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа к этому спортсмену'
                ], 403);
            }

            $data = $request->all();
            $data['used_sessions'] = $data['used_sessions'] ?? 0;
            $data['payment_description'] = $data['description'] ?? '';
            
            // Создаем запись в истории платежей
            $paymentHistory = $athlete->payment_history ?? [];
            
            // Переводим код типа пакета в нормальное название
            $packageTypeLabels = [
                'single' => 'Разовая тренировка',
                '4_sessions' => '4 тренировки',
                '8_sessions' => '8 тренировок',
                '12_sessions' => '12 тренировок',
                'unlimited' => 'Безлимит (месяц)',
                'custom' => 'Произвольное количество'
            ];
            
            $packageTypeLabel = $packageTypeLabels[$data['package_type']] ?? $data['package_type'];
            
            // Переводим способ оплаты
            $paymentMethodLabels = [
                'cash' => 'Наличные',
                'card' => 'Карта',
                'transfer' => 'Перевод',
                'other' => 'Другое'
            ];
            $paymentMethodLabel = $paymentMethodLabels[$data['payment_method']] ?? $data['payment_method'];
            
            $paymentHistory[] = [
                'id' => time(),
                'date' => $data['purchase_date'],
                'amount' => $data['package_price'],
                'description' => $data['payment_description'] ?: $packageTypeLabel,
                'payment_method' => $paymentMethodLabel
            ];

            // Пересчитываем общую сумму из истории платежей
            $totalPaid = array_sum(array_column($paymentHistory, 'amount'));
            
            // Пересчитываем общее количество тренировок из истории платежей
            $totalSessionsFromHistory = 0;
            foreach ($paymentHistory as $payment) {
                $description = $payment['description'] ?? '';
                $sessions = $this->extractSessionsFromDescription($description);
                $totalSessionsFromHistory += $sessions;
            }
            
            // Обновляем данные спортсмена
            $athlete->update([
                'package_type' => $packageTypeLabel,
                'total_sessions' => $totalSessionsFromHistory ?: $data['total_sessions'],
                'used_sessions' => $data['used_sessions'],
                'package_price' => $data['package_price'],
                'purchase_date' => $data['purchase_date'],
                'expires_date' => $data['expires_date'],
                'payment_method' => $paymentMethodLabel,
                'payment_description' => $data['payment_description'],
                'payment_history' => $paymentHistory,
                'total_paid' => $totalPaid,
                'last_payment_date' => $data['purchase_date'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Платеж успешно создан',
                'data' => $athlete->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании платежа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить платеж спортсмена
     */
    public function update(Request $request, $athleteId, $paymentId)
    {
        // Проверяем, что запрос ожидает JSON
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Expected JSON request'], 400);
        }

        $validator = Validator::make($request->all(), [
            'package_type' => 'required|string|max:255',
            'total_sessions' => 'required|integer|min:1',
            'used_sessions' => 'integer|min:0',
            'package_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'expires_date' => 'nullable|date',
            'payment_method' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $athlete = User::findOrFail($athleteId);
            
            // Проверяем, что текущий пользователь - тренер этого спортсмена
            if (Auth::user()->id !== $athlete->trainer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа к этому спортсмену'
                ], 403);
            }

            $data = $request->all();
            $data['used_sessions'] = $data['used_sessions'] ?? 0;
            $data['payment_description'] = $data['description'] ?? '';

            // Получаем переводы типов пакетов
            $packageTypeLabels = [
                'single' => 'Разовая тренировка',
                '4_sessions' => '4 тренировки',
                '8_sessions' => '8 тренировок',
                '12_sessions' => '12 тренировок',
                'unlimited' => 'Безлимит (месяц)',
                'custom' => 'Произвольное количество'
            ];
            $packageTypeLabel = $packageTypeLabels[$data['package_type']] ?? $data['package_type'];
            
            // Переводим способ оплаты
            $paymentMethodLabels = [
                'cash' => 'Наличные',
                'card' => 'Карта',
                'transfer' => 'Перевод',
                'other' => 'Другое'
            ];
            $paymentMethodLabel = $paymentMethodLabels[$data['payment_method']] ?? $data['payment_method'];

            // Обновляем конкретный платеж в истории
            $paymentHistory = $athlete->payment_history ?? [];
            $paymentIdInt = (int)$paymentId;
            
            foreach ($paymentHistory as &$payment) {
                if ((int)$payment['id'] === $paymentIdInt) {
                    $payment['date'] = $data['purchase_date'];
                    $payment['amount'] = $data['package_price'];
                    $payment['description'] = $data['payment_description'] ?: $packageTypeLabel;
                    $payment['payment_method'] = $paymentMethodLabel;
                    break;
                }
            }

            // Пересчитываем общую сумму из истории платежей
            $totalPaid = array_sum(array_column($paymentHistory, 'amount'));
            
            // Пересчитываем общее количество тренировок из истории платежей
            $totalSessionsFromHistory = 0;
            foreach ($paymentHistory as $payment) {
                $description = $payment['description'] ?? '';
                $sessions = $this->extractSessionsFromDescription($description);
                $totalSessionsFromHistory += $sessions;
            }
            
            // Обновляем данные спортсмена
            $athlete->update([
                'package_type' => $packageTypeLabel,
                'total_sessions' => $totalSessionsFromHistory ?: $data['total_sessions'],
                'used_sessions' => $data['used_sessions'],
                'package_price' => $data['package_price'],
                'purchase_date' => $data['purchase_date'],
                'expires_date' => $data['expires_date'],
                'payment_method' => $paymentMethodLabel,
                'payment_description' => $data['payment_description'],
                'payment_history' => $paymentHistory,
                'total_paid' => $totalPaid,
                'last_payment_date' => $data['purchase_date'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Платеж успешно обновлен',
                'data' => $athlete->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении платежа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить платеж спортсмена
     */
    public function destroy($athleteId, $paymentId)
    {
        try {
            $athlete = User::findOrFail($athleteId);
            
            // Проверяем, что текущий пользователь - тренер этого спортсмена
            if (Auth::user()->id !== $athlete->trainer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа к этому спортсмену'
                ], 403);
            }

            // Удаляем конкретный платеж из истории
            $paymentHistory = $athlete->payment_history ?? [];
            $paymentIdInt = (int)$paymentId;
            
            $paymentHistory = array_values(array_filter($paymentHistory, function($payment) use ($paymentIdInt) {
                return (int)($payment['id'] ?? 0) !== $paymentIdInt;
            }));
            
            // Пересчитываем данные из оставшейся истории
            if (count($paymentHistory) > 0) {
                // Пересчитываем общую сумму
                $totalPaid = array_sum(array_column($paymentHistory, 'amount'));
                
                // Пересчитываем общее количество тренировок
                $totalSessionsFromHistory = 0;
                foreach ($paymentHistory as $payment) {
                    $description = $payment['description'] ?? '';
                    $sessions = $this->extractSessionsFromDescription($description);
                    $totalSessionsFromHistory += $sessions;
                }
                
                // Получаем последний платеж (последний элемент массива)
                $lastPaymentDate = null;
                if (!empty($paymentHistory)) {
                    $lastPayment = $paymentHistory[count($paymentHistory) - 1];
                    $lastPaymentDate = $lastPayment['date'] ?? null;
                }
                
                $athlete->update([
                    'payment_history' => $paymentHistory,
                    'total_sessions' => $totalSessionsFromHistory,
                    'total_paid' => $totalPaid,
                    'last_payment_date' => $lastPaymentDate,
                ]);
            } else {
                // Если платежей не осталось, обнуляем финансовые данные
                $athlete->update([
                    'package_type' => null,
                    'total_sessions' => 0,
                    'used_sessions' => 0,
                    'package_price' => 0,
                    'purchase_date' => null,
                    'expires_date' => null,
                    'payment_method' => null,
                    'payment_description' => null,
                    'payment_history' => [],
                    'total_paid' => 0,
                    'last_payment_date' => null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Платеж успешно удален',
                'data' => $athlete->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении платежа: ' . $e->getMessage()
            ], 500);
        }
    }
}