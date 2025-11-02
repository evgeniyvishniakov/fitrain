<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainerSubscription;

class SubscriptionController extends Controller
{
    /**
     * Показать страницу подписки
     */
    public function index()
    {
        $user = auth()->user();
        
        // Получаем текущую активную подписку
        $currentSubscription = TrainerSubscription::where('trainer_id', $user->id)
            ->whereIn('status', ['active', 'trial'])
            ->where('expires_date', '>=', now())
            ->with(['plan', 'currency'])
            ->latest('expires_date')
            ->first();
        
        // Получаем историю всех подписок
        $subscriptionHistory = TrainerSubscription::where('trainer_id', $user->id)
            ->with(['plan', 'currency'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('crm.trainer.subscription.index', compact('currentSubscription', 'subscriptionHistory'));
    }
}
