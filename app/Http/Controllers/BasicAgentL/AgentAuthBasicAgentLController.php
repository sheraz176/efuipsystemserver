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
                 return view('basic-agent-l.dashboard', compact( 'agent'));

        }

    }

    public function getDashboardData(Request $request)
  {
    $agent = session('agent');

    if (!$agent) {
        return response()->json([
            'status' => 'error',
            'message' => 'Session Expired. Kindly Re-login.',
        ], 401);
    }

    $agentId = $agent->agent_id;

    $data = CustomerSubscription::select(
        DB::raw("COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 END) AS todaySubscriptionCount"),
        DB::raw("COUNT(CASE WHEN YEAR(created_at) = YEAR(CURRENT_DATE) AND MONTH(created_at) = MONTH(CURRENT_DATE) THEN 1 END) AS currentMonthSubscriptionCount"),
        DB::raw("COUNT(CASE WHEN YEAR(created_at) = YEAR(CURRENT_DATE) THEN 1 END) AS currentYearSubscriptionCount")
    )
    ->where('sales_agent', $agentId) // Filter by sales agent
    ->first();

    // Access the results
    $todaySubscriptionCount = $data->todaySubscriptionCount;
    $currentMonthSubscriptionCount = $data->currentMonthSubscriptionCount;
    $currentYearSubscriptionCount = $data->currentYearSubscriptionCount;

    return response()->json([
        'status' => 'success',
        'data' => [
            'todaySalesCount' => $todaySubscriptionCount,
            'currentMonthTotalCount' => $currentMonthSubscriptionCount,
            'currentYearTotal' => $currentYearSubscriptionCount,
        ],
    ]);
   }


}
