<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAgentLoginStatus
{
    public function handle($request, Closure $next)
    {
        $agent = Auth::guard('agent')->user();

        if ($agent && $agent->islogin == 0) {
            Auth::guard('agent')->logout();
            return redirect()->route('basic-agent.login')->withErrors(['login' => 'You have been logged out automatically.']);
        }

        return $next($request);
    }
}
