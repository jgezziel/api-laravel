<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Helpers\JwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = $request->header('Authorization');

        if ($jwt == null) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Token not received'
            ];

            return response()->json($data, $data['code']);
        }

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($jwt);

        if ($checkToken) {
            return $next($request);
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'The user is not authenticated.'
            ];

            return response()->json($data, $data['code']);
        }
    }
}
