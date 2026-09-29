<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\{ Setting };

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');
        $apiKey = Setting::getSettingValue('api_authorization');

        if (!empty($header)) {
            // Validate Bearer tokens against the devices table to prevent authentication bypass
            if (str_starts_with($header, 'Bearer ')) {
                $token = substr($header, 7);
                if (!empty($token) && \App\Models\Device::where('device_token', $token)->exists()) {
                    return $next($request);
                }
            } elseif (!empty($apiKey) && $apiKey === $header) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Unauthorized.'], 401);
    }
}
