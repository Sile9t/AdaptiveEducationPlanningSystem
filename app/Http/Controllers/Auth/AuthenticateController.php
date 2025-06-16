<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
class AuthenticateController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/login",
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
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
        
        if (MoonshineUser::where('email', $request->email)->first()) {
            $authenticated = MoonShineAuth::getGuard()->attempt(
                [
                    moonshineConfig()->getUserField('username', 'email') => $request->email,
                    moonshineConfig()->getUserField('password') => $request->password,
                ],
                $request->boolean('remember')
            );

            if ($authenticated) return redirect()->intended(
                moonshineRouter()->getEndpoints()->home()
            );
        }
        
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(30))->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => 30 * 60
        ]);
    }

    /**
     *  @OA\Post(
     *      path="/api/logout",
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
    public function destroy(Request $request)
    {
        Auth::logout();

        return redirect('/');
    }
}
