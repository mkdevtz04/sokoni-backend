<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return redirect()->route('admin.login');
        }

        // Single-device enforcement: if another device logged in, this session is stale
        if (Auth::user()->admin_session_id !== session()->getId()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')
                ->with('session_kicked', true);
        }

        return $next($request);
    }
}
