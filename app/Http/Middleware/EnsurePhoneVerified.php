<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->hasVerifiedPhone()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number not verified. Please verify your phone number first.',
            ], 403);
        }

        return $next($request);
    }
}
