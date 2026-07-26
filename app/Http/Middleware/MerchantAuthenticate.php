<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MerchantAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('merchant')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('merchant.login');
        }

        return $next($request);
    }
}
