<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackGate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        if (!Auth::guard('web')->check() && request()->segment(1) == 'admin') {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('web')->user();

        if ($user->roles->where('slug', 'admin')->count()) {
            return $next($request);
        }

        if ($user->status != 1) {
            Auth::guard('web')->logout();
            return redirect()->route('admin.login')->withErrors(['inactive' => 'Your account is inactive.']);
        }

        if ($permission && !$user->can($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
