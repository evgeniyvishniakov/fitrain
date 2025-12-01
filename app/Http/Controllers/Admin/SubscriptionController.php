<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\SubscriptionPlan;
use App\Models\Currency;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubscriptionController extends BaseController
{
    /**
     * Список планов подписок
     */
    public function index()
    {
        $subscriptions = SubscriptionPlan::with('currency')->active()->get();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Форма создания плана подписки
     */
    public function create()
    {
        $currencies = Currency::active()->ordered()->get();
        return view('admin.subscriptions.create', compact('currencies'));
    }

    /**
     * Создание плана подписки
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|exists:currencies,code',
            'is_active' => 'boolean'
        ]);

        $subscription = SubscriptionPlan::create($request->all());

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'План подписки успешно создан');
    }

    /**
     * Просмотр плана подписки
     */
    public function show($id)
    {
        $subscription = SubscriptionPlan::with('currency')->findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Форма редактирования плана подписки
     */
    public function edit($id)
    {
        $subscription = SubscriptionPlan::findOrFail($id);
        $currencies = Currency::active()->ordered()->get();
        return view('admin.subscriptions.edit', compact('subscription', 'currencies'));
    }

    /**
     * Обновление плана подписки
     */
    public function update(Request $request, $id)
    {
        $subscription = SubscriptionPlan::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|exists:currencies,code',
            'is_active' => 'boolean'
        ]);

        $subscription->update($request->all());

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'План подписки успешно обновлен');
    }

    /**
     * Удаление плана подписки
     */
    public function destroy($id)
    {
        $subscription = SubscriptionPlan::findOrFail($id);
        $subscription->delete();

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'План подписки успешно удален');
    }

    /**
     * Переключить статус
     */
    public function toggleStatus($id)
    {
        $subscription = SubscriptionPlan::findOrFail($id);
        $subscription->update(['is_active' => !$subscription->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $subscription->is_active,
            'message' => $subscription->is_active ? 'План подписки активирован' : 'План подписки деактивирован'
        ]);
    }

    /**
     * Сохранение настроек доната
     */
    public function donationSettings(Request $request)
    {
        $request->validate([
            'bank_card_number' => 'nullable|string|max:255',
            'bank_card_holder' => 'nullable|string|max:255',
            'bank_qr_code' => 'nullable|image|max:5120',
            'crypto_wallet_address' => 'nullable|string|max:255',
            'crypto_qr_code' => 'nullable|image|max:5120'
        ]);

        // Сохраняем текстовые данные
        if ($request->has('bank_card_number')) {
            SystemSetting::set('donation.bank_card_number', $request->bank_card_number, 'string', 'Номер банковской карты для доната', true);
        }
        if ($request->has('bank_card_holder')) {
            SystemSetting::set('donation.bank_card_holder', $request->bank_card_holder, 'string', 'Имя получателя для доната', true);
        }
        if ($request->has('crypto_wallet_address')) {
            SystemSetting::set('donation.crypto_wallet_address', $request->crypto_wallet_address, 'string', 'Адрес криптокошелька для доната', true);
        }

        // Загрузка QR-кода для банка
        if ($request->hasFile('bank_qr_code')) {
            $oldPath = SystemSetting::get('donation.bank_qr_code');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('bank_qr_code')->store('subscriptions/qr', 'public');
            SystemSetting::set('donation.bank_qr_code', $path, 'string', 'QR-код для банковской карты', true);
        }

        // Загрузка QR-кода для крипты
        if ($request->hasFile('crypto_qr_code')) {
            $oldPath = SystemSetting::get('donation.crypto_qr_code');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('crypto_qr_code')->store('subscriptions/qr', 'public');
            SystemSetting::set('donation.crypto_qr_code', $path, 'string', 'QR-код для криптокошелька', true);
        }

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'Настройки доната успешно сохранены');
    }
}
