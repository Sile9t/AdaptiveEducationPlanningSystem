<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineAuth;
use OpenApi\Annotations as OA;

/**
 *  @OA\Info(
 *      version="1.0.0",
 *      title="Authentication controller"
 *  )
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     *      path="/login",
     *      summary="Login a user",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string"             
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="remember",
     *                      type="boolean"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=302,
     *          description="Redirect"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      )
     * )
     * 
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
     *  @OA\Post(
     *      path="/logout",
     *      summary="Logout a user",
     *      @OA\RequestBody(),
     *      @OA\Response(
     *          response=302,
     *          description="Redirect"
     *      )
     *  )
     * 
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
