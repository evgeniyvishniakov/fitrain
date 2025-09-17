<?php

namespace App\Http\Controllers\Crm\Auth;

use App\Http\Controllers\Crm\Shared\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Shared\User;

class RegisterController extends BaseController
{
    /**
     * Показать форму регистрации
     */
    public function showRegistrationForm()
    {
        return view('crm.auth.register');
    }

    /**
     * Обработка регистрации
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('crm.dashboard.main');
    }
}
