<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MoonshineUser;
use Laravel\Sanctum\Contracts\HasApiTokens;
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
     *                      type="boolean",
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
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
        
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = self::createTokenForUser($user);

        return response()->json([
            'must_change_password' => $user->must_change_password,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => 30 * 60
        ]);
    }
    
    function createTokenForUser($user)
    {
        if (isset(Auth::user()->tokens))
            Auth::user()->tokens->each(function ($token, $key) {
                $token->delete();
            });
        
        return $user->createToken('auth_token', ['*'], now()->addMinutes(30))->plainTextToken;
    }

    /**
     *  @OA\Post(
     *      path="/api/logout",
     *      summary="Logout a user",
     *      @OA\Response(
     *          response=302,
     *          description="Redirect"
     *      )
     *  )
     * 
     * Destroy an authenticated token.
     */
    public function destroy(Request $request)
    {
        $request->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json([
            'message' => 'You are logged out'
        ]);
    }
}
