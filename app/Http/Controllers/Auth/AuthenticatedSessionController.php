<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use MoonShine\Laravel\Http\Requests\LoginFormRequest;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineAuth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $admin = MoonshineUser::where('email', $request['email'])->first();
        
        if ($admin) {
            if (MoonShineAuth::getGuard()->attempt(
                [
                    moonshineConfig()->getUserField('username', 'email') => $request['email'],
                    moonshineConfig()->getUserField('password') => $request['password'],
                ],
                $request->boolean('remember')
            )){
                return redirect()->intended(
                    moonshineRouter()->getEndpoints()->home()
                );
            }         
        }

        $request->authenticate();

        if (Auth::user()->must_change_password)
            return redirect()->route('change-password', ['email' => Auth::user()->email]);

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
