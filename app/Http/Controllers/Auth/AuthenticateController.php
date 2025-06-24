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

class AuthenticateController extends Controller
{
    /**
     * @OA\Post(
     *      tags={"api"},
     *      path="/api/login",
     *      operationId="login",
     *      @OA\RequestBody(
     *          description="User credentials",
     *          required=true,
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
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Some credentials are wrong or user doesn't exists"
     *      )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
        
        $user = MoonshineUser::with('moonshineUserRole')->where('email',$request->email)->first();
        $role = is_null($user) ? null : $user->moonshineUserRole->name;
        $user = $user ?? User::with("role")->where('email', $request->email)->first();
        $role = $role ?? (is_null($user) ? null : ($user->role->name));
        
        if (is_null($user)) {
            return response()->json([
                'email' => 'Email doesn\'t match any entry'
            ], 401);
        }

        if (is_a($user, 'App\Models\MoonshineUser')) {
            $authenticated = MoonShineAuth::getGuard()->attempt($credentials);
        }
        else {
            $authenticated = Auth::attempt($credentials);
        }

        if (! $authenticated) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = self::createTokenForUser($user);
        $must_change_password = is_null($user->must_change_password) ? false : $user->must_change_password;
        
        return response()->json([
            'user_role' => $role,
            'must_change_password' => $must_change_password,
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
     *      tags={"api"},
     *      path="/api/logout",
     *      operationId="logout",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     *  )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json([
            'message' => 'You are logged out'
        ]);
    }
}
