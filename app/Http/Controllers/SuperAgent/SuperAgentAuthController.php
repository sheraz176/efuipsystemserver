<?php

namespace App\Http\Controllers\SuperAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAgentAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('super_agent.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard('super_agent')->attempt($credentials)) {
            $agent = Auth::guard('super_agent')->user();
            session(['agent' => $agent]);
            return redirect()->intended(route('super_agent.dashboard'));
        }

        //return back()->withErrors(['message' => 'Invalid credentials']);
        return redirect()->back()->withInput()->withErrors(['login' => 'Invalid credentials, Kindly Check Your Username & Password, Password is Case Sensitive']);
    }

    public function logout(Request $request)
    {
        
        Auth::guard('super_agent')->logout();

        $request->session()->invalidate();

        // return view('super_agent.login');
        return redirect()->intended(route('super_agent.login'));
    }
}
