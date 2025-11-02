<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\SubscriptionPlan;
use App\Models\Currency;
use Illuminate\Http\Request;

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
}
