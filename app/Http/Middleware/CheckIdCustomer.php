<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckIdCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $profileId = (int) $request->route('id');

        if ($user->id !== $profileId || Auth::user()->role_id == 1) {
            \Log::info('Middleware CheckIdCustomer', [
                'user_id' => $user->id ?? null,
                'route_id' => $request->route('id'),
                'role_id' => $user->role_id ?? null,
            ]);            
            abort(403, 'Forbidden access.');
        }
        return $next($request);
    }
}
