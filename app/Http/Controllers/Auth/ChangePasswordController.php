<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ChangePasswordController extends Controller
{
    public function create(Request $request): Response
    {
        $email = Auth::user()->email;
        
        // return view('auth.change-password', ['email' => $email, 'request' => $request]);
        
        return Inertia::render('Auth/ChangePassword', [
            'status' => session('status')
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ]);

        $user = Auth::user();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
            'must_change_password' => false,
        ])->save();
        
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
