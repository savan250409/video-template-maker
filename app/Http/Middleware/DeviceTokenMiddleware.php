<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Device;

class DeviceTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Device token required.'], 401);
        }

        $device = Device::where('device_token', $token)->first();

        if (!$device) {
            return response()->json(['message' => 'Invalid or expired device token.'], 401);
        }

        $request->merge(['_authenticated_device' => $device]);

        return $next($request);
    }
}
