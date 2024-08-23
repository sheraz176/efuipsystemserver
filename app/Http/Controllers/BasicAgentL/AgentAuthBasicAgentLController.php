<?php

namespace App\Http\Controllers\BasicAgentL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TeleSalesAgent;
use Illuminate\Support\Facades\Log;

class AgentAuthBasicAgentLController extends Controller
{
    public function showLoginForm()
    {
        return view('basic-agent-l.login');
    }


    public function login(Request $request)
    {
        // Validate the login request
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Fetch the agent by username
        $agent = TeleSalesAgent::where('username', $request->username)->first();

        // Check if the agent exists and is active
        if ($agent) {
            if ($agent->status == 0) {
                return redirect()->back()->withInput()->withErrors(['login' => 'Your account is disabled.']);
            }

            // Attempt to authenticate the agent
            if (Auth::guard('agent')->attempt($credentials)) {
                Log::channel('login_log_basicagent')->info('Basic Agent logged in.', ['username' => $request->username]);

                // Authentication successful, update login details and redirect to the agent dashboard
                $agent = Auth::guard('agent')->user();
                $agent->islogin = 1;
                $agent->today_login_time = now();
                $agent->save();

                // Store agent information in the session
                session(['agent' => $agent]);

                return redirect()->route('basic-agent-l.dashboard');
            } else {
                // Invalid credentials
                return redirect()->back()->withInput()->withErrors(['login' => 'Invalid credentials, Kindly check your username & password.']);
            }
        } else {
            // Agent does not exist
            return redirect()->back()->withInput()->withErrors(['login' => 'No account found with this username.']);
        }
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
        Log::channel('login_log_basicagent')->info('Basic Agent log out.');

        return redirect()->route('basic-agent-l.login');
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

                 return view('basic-agent-l.dashboard', compact('currentMonthTotal', 'currentYearTotal', 'currentDayTotal','currentMonthTotalCount','currentDayTotalCount', 'agent'));

        }

    }
}
