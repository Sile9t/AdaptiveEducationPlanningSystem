<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use MoonShine\Laravel\Http\Requests\LoginFormRequest;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineAuth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $admin = MoonshineUser::where('email', $request['email'])->first();
        
        if ($admin) {
            // $moonshineRequest = new LoginFormRequest();
            // $moonshineRequest['username'] = $request['email']; 
            // $moonshineRequest['password'] = $request['password'];

            // if (filled(moonshineConfig()->getAuthPipelines())) {
            //     $moonshineRequest = Pipeline::send($moonshineRequest)->through(
            //         moonshineConfig()->getAuthPipelines()
            //     )->thenReturn();
            // }

            // // dd("Moonshine request: " . $moonshineRequest);
            // $moonshineRequest->authenticate();
            // return redirect()->intended(
            //     moonshineRouter()->getEndpoints()->home()
            // );

            $authenticated = MoonShineAuth::getGuard()->attempt(
                [
                    moonshineConfig()->getUserField('username', 'email') => $request['email'],
                    moonshineConfig()->getUserField('password') => $request['password'],
                ],
                $request->boolean('remember')
            );

            $request->session()->regenerate();

            if ($authenticated) {
                return redirect()->intended(
                    moonshineRouter()->getEndpoints()->home()
                );
            }         
        }

        $request->authenticate();
        $request->session()->regenerate();
        if (Auth::user()->must_change_password == true) {
            return redirect()->intended('change-password');
        }

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
