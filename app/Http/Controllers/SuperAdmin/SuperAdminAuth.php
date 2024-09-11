<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\TeleSalesAgent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Company\CompanyProfile;
use Illuminate\Support\Facades\Log;
use App\Models\RecusiveChargingData;

class SuperAdminAuth extends Controller
{
    protected $redirectTo = '/superadmin/dashboard';

    public function showLoginForm()
    {
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('super_admin')->attempt($request->only('username', 'password'))) {

            Log::channel('super_admin_log')->info('Super Admin logged in.', ['username' => $request->username]);

            $Superadmin = Auth::guard('super_admin')->user();

            session(['Superadmin' => $Superadmin]);
            return redirect()->route('superadmin.dashboard');

        }


        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function showDashboard()
    {

        // Count of today's subscriptions
        $todaySubscriptionCount = CustomerSubscription::whereDate('created_at', Carbon::today())
            ->count();

        // Count of current month's subscriptions
        $currentMonthSubscriptionCount = CustomerSubscription::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Count of current year's subscriptions
        $currentYearSubscriptionCount = CustomerSubscription::whereYear('created_at', Carbon::now()->year)
            ->count();

            $NetEnrollmentCount = CustomerSubscription::where('policy_status','1')
            ->count();

            $dailyTransactionSum = CustomerSubscription::whereDate('created_at', Carbon::today())
            ->sum('transaction_amount');

        // Sum of monthly transaction amounts
        $monthlyTransactionSum = CustomerSubscription::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('transaction_amount');

        // Sum of yearly transaction amounts
        $yearlyTransactionSum = CustomerSubscription::whereYear('created_at', Carbon::now()->year)
            ->sum('transaction_amount');

              // Get total recusive charging count
        $TotalRecusiveChargingCount = RecusiveChargingData::count();

          // Get today's recusive charging count
        $TodayRecusiveChargingCount = RecusiveChargingData::whereDate('created_at', now()->toDateString())->count();

         // Get last month's recusive charging count
         $LastMonthRecusiveChargingCount = RecusiveChargingData::whereMonth('created_at', now()->subMonth()->month)
          ->whereYear('created_at', now()->subMonth()->year)
           ->count();


            $companies = CompanyProfile::all();
            //  dd($activeTsm);

        return view('superadmin.dashboard', [
            'todaySubscriptionCount' => $todaySubscriptionCount,
            'currentMonthSubscriptionCount' => $currentMonthSubscriptionCount,
            'currentYearSubscriptionCount' => $currentYearSubscriptionCount,
            'dailyTransactionSum' => $dailyTransactionSum,
            'monthlyTransactionSum' => $monthlyTransactionSum,
            'yearlyTransactionSum' => $yearlyTransactionSum,
            'NetEnrollmentCount' => $NetEnrollmentCount,
            'companies' => $companies,
            'TotalRecusiveChargingCount' => $TotalRecusiveChargingCount,
            'TodayRecusiveChargingCount'=> $TodayRecusiveChargingCount,
            'LastMonthRecusiveChargingCount' => $LastMonthRecusiveChargingCount,
        ]);

    }

    public function logout()
    {
        Auth::guard('super_admin')->logout();
        Log::channel('super_admin_log')->info('Super Admin log out.');

        return redirect()->route('superadmin.login');
    }

    public function getStats()
    {
        $stats = [

            'totalTsm' => TeleSalesAgent::where('company_id', '11')->where('status','1')->count(),
            'activeTsm' => TeleSalesAgent::where('company_id', '11')->where('islogin', '1')->count(),

            'totalIbex' => TeleSalesAgent::where('company_id', '1')->where('status','1')->count(),
            'activeIbex' => TeleSalesAgent::where('company_id', '1')->where('islogin', '1')->count(),

            'totalAbacus' => TeleSalesAgent::where('company_id', '2')->where('status','1')->count(),
            'activeAbacus' => TeleSalesAgent::where('company_id', '2')->where('islogin', '1')->count(),

            'totalSybrid' => TeleSalesAgent::where('company_id', '12')->where('status','1')->count(),
            'activeSybrid' => TeleSalesAgent::where('company_id', '12')->where('islogin', '1')->count(),

            'totalJazzIVR' => TeleSalesAgent::where('company_id', '14')->where('status','1')->count(),
            'activeJazzIVR' => TeleSalesAgent::where('company_id', '14')->where('islogin', '1')->count(),

            'totalactive' => TeleSalesAgent::where('status','1')->count(),
            'totallive' => TeleSalesAgent::where('islogin', '1')->count(),

            'netentrollmentrevinus' => number_format(CustomerSubscription::where('policy_status', '1')->sum('transaction_amount'), 2),


        ];

        return response()->json($stats);
    }


}
