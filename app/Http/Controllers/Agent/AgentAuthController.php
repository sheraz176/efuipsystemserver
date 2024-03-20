<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TeleSalesAgent;

class AgentAuthController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->guard('agent')->check()) {
            return redirect()->route('agent.dashboard');
        }
        return view('agent.login');
    }

    /**
     * Handle agent login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the agent
        $agent = TeleSalesAgent::where('username', $request->username)->first();

    if ($agent && $agent->status == 1 && Auth::guard('agent')->attempt($credentials)) {
        // Authentication successful, update login details and redirect to the agent dashboard
        $agent = Auth::guard('agent')->user();
        $agent->islogin = 1;
        $agent->today_login_time = now();
        $agent->save();

        session(['agent' => $agent]);

        $agentId = $agent->agent_id;


        $currentMonthTotal = CustomerSubscription::where('sales_agent', $agentId)
            ->whereMonth('subscription_time', Carbon::now()->month)
            ->sum('transaction_amount');
        
        $currentMonthTotalCount = CustomerSubscription::where('sales_agent', $agentId)
            ->whereMonth('subscription_time', Carbon::now()->month)
            ->count();
            //dd($currentMonthTotal);

        $currentYearTotal = CustomerSubscription::where('sales_agent', $agentId)
            ->whereYear('subscription_time', Carbon::now()->year)
            ->sum('transaction_amount');  
            
        $currentDayTotal = CustomerSubscription::where('sales_agent', $agentId)
            ->whereDate('subscription_time', Carbon::now()->toDateString())
            ->sum('transaction_amount');

        $currentDayTotalCount = CustomerSubscription::where('sales_agent', $agentId)
            ->whereDate('subscription_time', Carbon::now()->toDateString())
            ->count();

             return view('agent.dashboard', compact('currentMonthTotal', 'currentYearTotal', 'currentDayTotal','currentMonthTotalCount','currentDayTotalCount', 'agent'));
            //return redirect()->route('agent.dashboard');
            
        }
        if ($agent && $agent->status == 0) {
            return redirect()->back()->withInput()->withErrors(['login' => 'Your account is disabled.']);
        }

        // Authentication failed, redirect back with errors
        return redirect()->back()->withInput()->withErrors(['login' => 'Invalid credentials, Kindly Check Your Username & Password, Password is Case Sensitive']);
    }

    /**
     * Logout the authenticated agent.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $agent = Auth::guard('agent')->user();

    // Check if the agent is logged in
        if ($agent) {
            $agent->islogin = 0;
            $agent->today_logout_time = now();
            $agent->save();
        }

        Auth::guard('agent')->logout();

        return redirect()->route('agent.login');
    }

    /**
     * Show the agent dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
       


        $agent = session('agent');

        if (!$agent) {
            
            return redirect()->back()->withInput()->withErrors(['login' => 'Session Expired Kindly Re-login']);
        }
        else{
            $agentId = $agent->agent_id;
            $currentMonthTotal = CustomerSubscription::where('sales_agent', $agentId)
                ->whereMonth('subscription_time', Carbon::now()->month)
                ->sum('transaction_amount');
            
            $currentMonthTotalCount = CustomerSubscription::where('sales_agent', $agentId)
                ->whereMonth('subscription_time', Carbon::now()->month)
                ->count();
                //dd($currentMonthTotal);
    
            $currentYearTotal = CustomerSubscription::where('sales_agent', $agentId)
                ->whereYear('subscription_time', Carbon::now()->year)
                ->sum('transaction_amount');  
                
            $currentDayTotal = CustomerSubscription::where('sales_agent', $agentId)
                ->whereDate('subscription_time', Carbon::now()->toDateString())
                ->sum('transaction_amount');
    
            $currentDayTotalCount = CustomerSubscription::where('sales_agent', $agentId)
                ->whereDate('subscription_time', Carbon::now()->toDateString())
                ->count();
    
                 return view('agent.dashboard', compact('currentMonthTotal', 'currentYearTotal', 'currentDayTotal','currentMonthTotalCount','currentDayTotalCount', 'agent'));
              
        }
       
    }
}
