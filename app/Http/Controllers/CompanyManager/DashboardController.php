<?php

namespace App\Http\Controllers\CompanyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Refund\RefundedCustomer;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\Subscription\CustomerSubscription;
use Carbon\Carbon;
use App\Models\TeleSalesAgent;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{

    public function index()
    {
        return view('company_manager.dashboard');
    }

    public function today_interested_customer(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $customer = InterestedCustomer::whereDate('created_at', Carbon::today())
        ->where('company_id' , $companyId)->get();
        // dd($customer);
        return view('company_manager.interested-customer.today_interested_customer',compact('customer'));
    }
    public function today_deduction_interested_customer(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $customer = InterestedCustomer::whereDate('created_at', Carbon::today())
        ->where('company_id' , $companyId)->where('deduction_applied', 1)->get();
        // dd($customer);
        return view('company_manager.interested-customer.today_deduction_interested_customer',compact('customer'));

    }


    public function ajex(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;

        // Batch queries for TeleSalesAgent
        $teleSalesAgentStats = TeleSalesAgent::selectRaw("
                SUM(CASE WHEN category = 0 AND islogin = 1 THEN 1 ELSE 0 END) AS liveAgents,
                SUM(CASE WHEN category = 0 AND status = 1 THEN 1 ELSE 0 END) AS activeAgents,
                SUM(CASE WHEN category = 1 AND islogin = 1 THEN 1 ELSE 0 END) AS liveAgentsWFH,
                SUM(CASE WHEN category = 1 AND status = 1 THEN 1 ELSE 0 END) AS activeAgentsWFH
            ")
            ->where('company_id', $companyId)
            ->first();

        // Batch queries for CustomerSubscription
        $subscriptionStats = CustomerSubscription::selectRaw("
                COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 END) AS todaySubscriptionCount,
                COUNT(CASE WHEN YEAR(created_at) = YEAR(CURRENT_DATE) AND MONTH(created_at) = MONTH(CURRENT_DATE) THEN 1 END) AS currentMonthSubscriptionCount,
                COUNT(CASE WHEN YEAR(created_at) = YEAR(CURRENT_DATE) THEN 1 END) AS currentYearSubscriptionCount,
                SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN transaction_amount ELSE 0 END) AS dailyTransactionSum,
                SUM(CASE WHEN YEAR(created_at) = YEAR(CURRENT_DATE) AND MONTH(created_at) = MONTH(CURRENT_DATE) THEN transaction_amount ELSE 0 END) AS monthlyTransactionSum,
                SUM(CASE WHEN YEAR(created_at) = YEAR(CURRENT_DATE) THEN transaction_amount ELSE 0 END) AS yearlyTransactionSum
            ")
            ->where('company_id', $companyId)
            ->first();

        // Format the results for response
        return response()->json([
            'liveAgents' => number_format($teleSalesAgentStats->liveAgents),
            'activeAgents' => number_format($teleSalesAgentStats->activeAgents),
            'liveAgentsWFH' => number_format($teleSalesAgentStats->liveAgentsWFH),
            'activeAgentsWFH' => number_format($teleSalesAgentStats->activeAgentsWFH),
            'todaySubscriptionCount' => number_format($subscriptionStats->todaySubscriptionCount),
            'currentMonthSubscriptionCount' => number_format($subscriptionStats->currentMonthSubscriptionCount),
            'currentYearSubscriptionCount' => number_format($subscriptionStats->currentYearSubscriptionCount),
            'dailyTransactionSum' => number_format($subscriptionStats->dailyTransactionSum, 2),
            'monthlyTransactionSum' => number_format($subscriptionStats->monthlyTransactionSum, 2),
            'yearlyTransactionSum' => number_format($subscriptionStats->yearlyTransactionSum, 2),
        ]);
    }


public function NetEnrollment(Request $request)
{
    $companyId = Auth::guard('company_manager')->user()->company_id;

    // Get Customer Subscriptions for the last 30 days
    $Customer_Subscriptions = CustomerSubscription::where('subscription_time', '>=', Carbon::now()->subDays(30))
        ->where('policy_status', '1')
        ->where('company_id', $companyId)
        ->get();

    // Initialize an array to store daily subscription counts
    $CustomerSubscriptionData = [];

    // Loop through the subscriptions to populate the daily counts
    foreach ($Customer_Subscriptions as $CustomerSubscription) {
        $date = Carbon::parse($CustomerSubscription->created_at)->toDateString(); // Group by day

        if (!isset($CustomerSubscriptionData[$date])) {
            $CustomerSubscriptionData[$date] = 0;
        }

        $CustomerSubscriptionData[$date]++;
    }

    // Ensure the array contains all dates in the last 30 days, even if the count is zero
    $last30Days = collect();
    for ($i = 29; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->toDateString();
        $last30Days->put($date, $CustomerSubscriptionData[$date] ?? 0);
    }

    // Prepare the data for the line chart
    $dates = $last30Days->keys();
    $enrollments = $last30Days->values();

    // Return data as JSON response for the chart
    return response()->json([
        'dates' => $dates,
        'enrollments' => $enrollments,
    ]);
}

public function RefundedCustomers(Request $request)
{
    $companyId = Auth::guard('company_manager')->user()->company_id;

    // Get refunded customers for the last 30 days
    $Refunded_Customers = RefundedCustomer::join('customer_subscriptions', 'refunded_customers.subscription_id', '=', 'customer_subscriptions.subscription_id')
        ->where('refunded_customers.refunded_time', '>=', Carbon::now()->subDays(30))
        ->where('customer_subscriptions.company_id', $companyId)
        ->get();

    // Initialize an array to store daily refund counts
    $RefundedCustomersData = [];

    // Loop through the refunds to populate the daily counts
    foreach ($Refunded_Customers as $RefundedCustomer) {
        $date = Carbon::parse($RefundedCustomer->refunded_time)->toDateString(); // Group by unsubscription date

        if (!isset($RefundedCustomersData[$date])) {
            $RefundedCustomersData[$date] = 0;
        }

        $RefundedCustomersData[$date]++;
    }

    // Ensure the array contains all dates in the last 30 days, even if the count is zero
    $last30Days = collect();
    for ($i = 29; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->toDateString();
        $last30Days->put($date, $RefundedCustomersData[$date] ?? 0);
    }

    // Prepare the data for the line chart
    $dates = $last30Days->keys();
    $refunds = $last30Days->values();

    // Return data as JSON response for the chart
    return response()->json([
        'dates' => $dates,
        'refunds' => $refunds,
    ]);
}


public function ActiveAgent(Request $request)
{
    return view('company_manager.active-agent');


}

public function AgentData(Request $request)
{
    $companyId = Auth::guard('company_manager')->user()->company_id;

   //   dd('hi');
    if ($request->ajax()) {
        $data = TelesalesAgent::select('*')
        ->where('status',1)
        ->where('company_id', $companyId)
        ->where('category', '0');
        return Datatables::of($data)



   ->addColumn('islogin', function ($data) {
       if ($data->islogin == "1") {
           return '<button type="button" class="btn btn-success btn-sm">Log In</button>';
       }
       else{
           return '<button type="button" class="btn btn-danger btn-sm">Log Out</button>';
       }
   })

    ->rawColumns(['islogin'])
                ->make(true);
    }

}


}
