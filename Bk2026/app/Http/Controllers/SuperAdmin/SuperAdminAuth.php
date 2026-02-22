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
use App\Models\ConsentData;
use App\Models\SuperDash;

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



            $companies = CompanyProfile::all();
            $superDashRecord = SuperDash::first();
            //  dd($activeTsm);

        return view('superadmin.dashboard', [
            'companies' => $companies,
            'superDashRecord' => $superDashRecord,
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

            'totalTsm' => TeleSalesAgent::where('company_id', '11')->where('category', '0')->where('status','1')->count(),
            'activeTsm' => TeleSalesAgent::where('company_id', '11')->where('category', '0')->where('islogin', '1')->count(),

            'totalTsmWfh' => TeleSalesAgent::where('company_id', '11')->where('category', '1')->where('status','1')->count(),
            'activeTsmWfh' => TeleSalesAgent::where('company_id', '11')->where('category', '1')->where('islogin', '1')->count(),


            'totalIbex' => TeleSalesAgent::where('company_id', '1')->where('status','1')->count(),
            'activeIbex' => TeleSalesAgent::where('company_id', '1')->where('islogin', '1')->count(),

            'totalAbacus' => TeleSalesAgent::where('company_id', '2')->where('status','1')->count(),
            'activeAbacus' => TeleSalesAgent::where('company_id', '2')->where('islogin', '1')->count(),

            'totalSybrid' => TeleSalesAgent::where('company_id', '12')->where('status','1')->count(),
            'activeSybrid' => TeleSalesAgent::where('company_id', '12')->where('islogin', '1')->count(),

            'totalJazzIVR' => TeleSalesAgent::where('company_id', '14')->where('status','1')->count(),
            'activeJazzIVR' => TeleSalesAgent::where('company_id', '14')->where('islogin', '1')->count(),

            'totalWaada' => TeleSalesAgent::where('company_id', '19')->where('status','1')->count(),
            'activeWaadaIVR' => TeleSalesAgent::where('company_id', '19')->where('islogin', '1')->count(),

            'totalactive' => TeleSalesAgent::where('status','1')->count(),
            'totallive' => TeleSalesAgent::where('islogin', '1')->count(),

            'netentrollmentrevinus' => number_format(CustomerSubscription::where('policy_status', '1')->sum('transaction_amount'), 2),

            'todaySubscriptionCount' => number_format(CustomerSubscription::whereDate('created_at', Carbon::today())->count()),
            'currentMonthSubscriptionCount' => number_format(CustomerSubscription::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
             ->count()),

            'currentYearSubscriptionCount' => number_format(CustomerSubscription::whereYear('created_at', Carbon::now()->year)->count()),
            'NetEnrollmentCount' => number_format(CustomerSubscription::where('policy_status', '1')->count()),
            'dailyTransactionSum' => number_format(CustomerSubscription::whereDate('created_at', Carbon::today())->sum('transaction_amount')),
            'monthlyTransactionSum' => number_format(CustomerSubscription::whereYear('created_at', Carbon::now()->year)
             ->whereMonth('created_at', Carbon::now()->month)
             ->sum('transaction_amount')),

            'yearlyTransactionSum' => number_format(CustomerSubscription::whereYear('created_at', Carbon::now()->year)->sum('transaction_amount')),
            'TotalRecusiveChargingCount' => number_format(RecusiveChargingData::count()),
            'TodayRecusiveChargingCount' => number_format(RecusiveChargingData::whereDate('created_at', now()->toDateString())
              ->where('cps_response', 'Process service request successfully.')
              ->count()),

            'LastMonthRecusiveChargingCount' => number_format(RecusiveChargingData::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count()),

             'TodaySubscriptionsCount' => number_format(ConsentData::whereDate('created_at', now()->toDateString())->where('status', 'Success')->count()),
             'TotalSubscriptionCount' => number_format(ConsentData::where('status', 'Success')->count()),
             'TotalCount' => number_format(ConsentData::whereDate('created_at', now()->toDateString())->count()),



        ];

              // Check if a record already exists in SuperDash
    $superDashRecord = SuperDash::first();

    if ($superDashRecord) {
        // Update the existing record with all stats
        $superDashRecord->update($stats);
    } else {
        // Create a new record with all stats
        SuperDash::create($stats);
    }
    //  dd($stats);
        return response()->json($stats);
    }


}
