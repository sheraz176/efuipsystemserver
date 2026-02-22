<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeleSaleAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('agent')->check()) {
            return $next($request);
        }

        // If not authenticated as an agent, you may redirect to a login page or show an unauthorized view.
        // For simplicity, we'll just abort with a 403 status code for unauthorized access.
        //abort_unless(auth()->guard('agent')->check(), 403, 'Unauthorized access');
        return redirect()->route('agent.login')->with('error', 'You are not authorized to access this page.');

    }
}
