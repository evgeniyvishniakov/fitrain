<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Показать страницу подписки
     */
    public function index()
    {
        return view('crm.trainer.subscription.index');
    }
}
