<?php

namespace App\Http\Controllers\SuperAgentInterested;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuperAgent\SuperAgentModel;
use Illuminate\Support\Facades\Log;

class SuperAgentAuthControllerInterested extends Controller
{
    public function showLoginForm()
    {
        return view('super_agent_Interested.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

         $superagents =  SuperAgentModel::where('company_id',12)->where('username',$request->username)->first();


        //   dd($superagents);
        if (Auth::guard('super_agent')->attempt($credentials)) {

            Log::channel('login_log_superagentInterested')->info('Super Agent Interested logged in.', ['username' => $request->username]);

            $agent = Auth::guard('super_agent')->user();
            session(['agent' => $agent]);

            if ($superagents && $superagents->company_id == 12) {
                return redirect()->intended(route('super_agent_l.dashboard'));
            } else {
                return redirect()->intended(route('super_agent_interested.dashboard'));
            }

        }

        //return back()->withErrors(['message' => 'Invalid credentials']);
        return redirect()->back()->withInput()->withErrors(['login' => 'Invalid credentials, Kindly Check Your Username & Password, Password is Case Sensitive']);
    }

    public function logout(Request $request)
    {

        Auth::guard('super_agent')->logout();

        Log::channel('login_log_superagentInterested')->info('Super Agent Interested log out.');

        $request->session()->invalidate();

        // return view('super_agent.login');
        return redirect()->intended(route('super_agent_interested.login'));
    }
}
