<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\TrainerSubscription;
use App\Models\SubscriptionPlan;
use App\Models\Shared\User;
use App\Models\Currency;
use Illuminate\Http\Request;

class TrainerSubscriptionController extends BaseController
{
    /**
     * Список подписок тренеров
     */
    public function index(Request $request)
    {
        $query = TrainerSubscription::with(['trainer', 'plan', 'currency'])
            ->orderBy('expires_date', 'desc');

        // Фильтрация по статусу
        if ($request->filled('status')) {
            if ($request->status === 'expiring_soon') {
                $query->expiringSoon();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Поиск по имени тренера
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('trainer', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->paginate(20);
        
        // Статистика
        $stats = [
            'total' => TrainerSubscription::count(),
            'active' => TrainerSubscription::where('status', 'active')->count(),
            'trial' => TrainerSubscription::where('status', 'trial')->count(),
            'expired' => TrainerSubscription::where('status', 'expired')->orWhere(function($q) {
                $q->where('status', 'trial')
                  ->where('expires_date', '<', now());
            })->count(),
            'expiring_soon' => TrainerSubscription::expiringSoon()->count(),
        ];

        return view('admin.trainer-subscriptions.index', compact('subscriptions', 'stats'));
    }

    /**
     * Форма создания подписки
     */
    public function create()
    {
        $trainers = User::whereHas('roles', function($q) {
            $q->where('name', 'trainer');
        })->where('is_active', true)->orderBy('name')->get();
        
        $plans = SubscriptionPlan::active()->get();
        $currencies = Currency::active()->ordered()->get();
        
        return view('admin.trainer-subscriptions.create', compact('trainers', 'plans', 'currencies'));
    }

    /**
     * Создание подписки
     */
    public function store(Request $request)
    {
        $request->validate([
            'trainer_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|exists:currencies,code',
            'start_date' => 'required|date',
            'expires_date' => 'required|date|after:start_date',
            'is_trial' => 'boolean',
            'trial_days' => 'integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Определяем статус
        $status = $request->is_trial ? 'trial' : 'active';

        $subscription = TrainerSubscription::create([
            'trainer_id' => $request->trainer_id,
            'subscription_plan_id' => $request->subscription_plan_id,
            'status' => $status,
            'price' => $request->price,
            'currency_code' => $request->currency_code,
            'start_date' => $request->start_date,
            'expires_date' => $request->expires_date,
            'is_trial' => $request->is_trial ?? false,
            'trial_days' => $request->trial_days ?? 7,
            'notes' => $request->notes
        ]);

        return redirect()
            ->route('admin.trainer-subscriptions.index')
            ->with('success', 'Подписка тренера успешно создана');
    }

    /**
     * Форма редактирования подписки
     */
    public function edit($id)
    {
        $subscription = TrainerSubscription::with(['trainer', 'plan', 'currency'])->findOrFail($id);
        
        $trainers = User::whereHas('roles', function($q) {
            $q->where('name', 'trainer');
        })->where('is_active', true)->orderBy('name')->get();
        
        $plans = SubscriptionPlan::active()->get();
        $currencies = Currency::active()->ordered()->get();
        
        return view('admin.trainer-subscriptions.edit', compact('subscription', 'trainers', 'plans', 'currencies'));
    }

    /**
     * Обновление подписки
     */
    public function update(Request $request, $id)
    {
        $subscription = TrainerSubscription::findOrFail($id);
        
        $request->validate([
            'trainer_id' => 'required|exists:users,id',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|exists:currencies,code',
            'start_date' => 'required|date',
            'expires_date' => 'required|date|after:start_date',
            'is_trial' => 'boolean',
            'trial_days' => 'integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Определяем статус
        $status = $request->is_trial ? 'trial' : 'active';

        $subscription->update([
            'trainer_id' => $request->trainer_id,
            'subscription_plan_id' => $request->subscription_plan_id,
            'status' => $status,
            'price' => $request->price,
            'currency_code' => $request->currency_code,
            'start_date' => $request->start_date,
            'expires_date' => $request->expires_date,
            'is_trial' => $request->is_trial ?? false,
            'trial_days' => $request->trial_days ?? 7,
            'notes' => $request->notes
        ]);

        return redirect()
            ->route('admin.trainer-subscriptions.index')
            ->with('success', 'Подписка тренера успешно обновлена');
    }

    /**
     * Просмотр подписки
     */
    public function show($id)
    {
        $subscription = TrainerSubscription::with(['trainer', 'plan', 'currency'])->findOrFail($id);
        return view('admin.trainer-subscriptions.show', compact('subscription'));
    }

    /**
     * Удаление подписки
     */
    public function destroy($id)
    {
        $subscription = TrainerSubscription::findOrFail($id);
        $subscription->delete();

        return redirect()
            ->route('admin.trainer-subscriptions.index')
            ->with('success', 'Подписка тренера успешно удалена');
    }
}
