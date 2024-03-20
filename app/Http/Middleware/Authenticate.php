<?php

namespace App\Http\Middleware;
use Illuminate\Support\Arr;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Check the guard and redirect accordingly
            $guardName = config('auth.defaults.guard', 'web');
           // dd($guardName);
    
            switch ($guardName) {
                case 'super_admins':
                    return route('superadmin.login');
                    break;
    
                case 'agent':
                    return route('agent.login');
                    break;
    
                // Add more cases for other guards if needed
    
                default:
                    return route('superadmin.login');
                    break;
            }
        }
    }
}
