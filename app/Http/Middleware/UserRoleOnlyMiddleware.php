<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserRoleOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = Auth::user()->role ?? '';

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        return response()->json([
            'errors' => [
                'message' => [
                    'You are not authorized to perform this action.'
                ]
            ]
        ], 403);
    }
}
