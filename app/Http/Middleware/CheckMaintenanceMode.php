<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Setting::isMaintenanceMode() && !$request->user()?->isAdmin()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'maintenance',
                    'message' => Setting::maintenanceMessage(),
                ], 503);
            }

            return response()->view('errors.maintenance', ['message' => Setting::maintenanceMessage()], 503);
        }

        return $next($request);
    }
}
