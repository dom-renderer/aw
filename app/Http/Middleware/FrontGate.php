<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontGate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        if (!Auth::guard('customer')->check() && request()->segment(1) == 'customer') {
            return redirect()->route('login');
        }

        $user = Auth::guard('customer')->user();

        if ($user->status != 1) {
            Auth::guard('customer')->logout();
            return redirect()->route('login')->withErrors(['inactive' => 'Your account is inactive.']);
        }

        if ($permission && !$user->can($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
