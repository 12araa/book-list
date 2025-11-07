<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class SetUserIdentifier
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('user_identifier')) {
            // ex: user_ab3921
            session(['user_identifier' => 'user_' . Str::random(6)]);
        }

        return $next($request);
    }
}
