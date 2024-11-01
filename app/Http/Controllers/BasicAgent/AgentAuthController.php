<?php

namespace App\Http\Controllers\BasicAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TeleSalesAgent;
use Illuminate\Support\Facades\Log;

class AgentAuthController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->guard('agent')->check()) {
            return redirect()->route('basic-agent.dashboard');
        }
        return view('basic-agent.login');
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

        $basicagents =  TeleSalesAgent::where('company_id', 11)->where('username', $request->username)->first();
        if ($basicagents && $basicagents->company_id == 11) {
            // dd('tsm');
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
        } else {
            if ($agent && $agent->status == 1 && Auth::guard('agent')->attempt($credentials)) {

                Log::channel('login_log_basicagent')->info('Basic Agent logged in.', ['username' => $request->username]);

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

                return view('basic-agent.dashboard', compact('currentMonthTotal', 'currentYearTotal', 'currentDayTotal', 'currentMonthTotalCount', 'currentDayTotalCount', 'agent'));
                //return redirect()->route('agent.dashboard');

            }
            if ($agent && $agent->status == 0) {
                return redirect()->back()->withInput()->withErrors(['login' => 'Your account is disabled.']);
            }

            // Authentication failed, redirect back with errors
            return redirect()->back()->withInput()->withErrors(['login' => 'Invalid credentials, Kindly Check Your Username & Password, Password is Case Sensitive']);
        }
    }

    /**
     * Logout the authenticated agent.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $agent = Auth::guard('agent')->user();

            if ($agent && $agent->islogin == 0) {
                Auth::guard('agent')->logout();
                return redirect()->route('basic-agent.login')->withErrors(['login' => 'You have been logged out automatically.']);
            }

            return $next($request);
        });
    }

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

        return redirect()->route('basic-agent.login');
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
        } else {
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

            return view('basic-agent.dashboard', compact('currentMonthTotal', 'currentYearTotal', 'currentDayTotal', 'currentMonthTotalCount', 'currentDayTotalCount', 'agent'));
        }
    }
}
