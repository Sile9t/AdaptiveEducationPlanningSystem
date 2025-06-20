<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            if (! is_null($request->user()) && $request->user()->tokenCan('[*]')) return redirect()->next();

            return 'You are not logged or token is expired';
        }

        return $request->expectsJson() ? 'You are not logged or token is expired' : null;
    }
}
