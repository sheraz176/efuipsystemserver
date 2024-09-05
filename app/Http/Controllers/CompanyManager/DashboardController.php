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

    $todaySubscriptionCount = CustomerSubscription::where('company_id', $companyId)
        ->whereDate('created_at', Carbon::today())
        ->count();

    // Count of live agents (currently logged in)
    $liveAgents = TeleSalesAgent::where('company_id', $companyId)
        ->where('islogin', '1')
        ->count();

    // Count of active agents
    $activeAgents = TeleSalesAgent::where('company_id', $companyId)
        ->where('status', '1')
        ->count();

    // Count of current month's subscriptions
    $currentMonthSubscriptionCount = CustomerSubscription::where('company_id', $companyId)
        ->whereYear('created_at', Carbon::now()->year)
        ->whereMonth('created_at', Carbon::now()->month)
        ->count();

    // Count of current year's subscriptions
    $currentYearSubscriptionCount = CustomerSubscription::where('company_id', $companyId)
        ->whereYear('created_at', Carbon::now()->year)
        ->count();

    // Sum of today's transactions
    $dailyTransactionSum = CustomerSubscription::where('company_id', $companyId)
        ->whereDate('created_at', Carbon::today())
        ->sum('transaction_amount');

    // Sum of monthly transaction amounts
    $monthlyTransactionSum = CustomerSubscription::where('company_id', $companyId)
        ->whereYear('created_at', Carbon::now()->year)
        ->whereMonth('created_at', Carbon::now()->month)
        ->sum('transaction_amount');

    // Sum of yearly transaction amounts
    $yearlyTransactionSum = CustomerSubscription::where('company_id', $companyId)
        ->whereYear('created_at', Carbon::now()->year)
        ->sum('transaction_amount');

    // Format the numbers before returning
    return response()->json([
        'liveAgents' => number_format($liveAgents),
        'todaySubscriptionCount' => number_format($todaySubscriptionCount),
        'activeAgents' => number_format($activeAgents),
        'currentMonthSubscriptionCount' => number_format($currentMonthSubscriptionCount),
        'currentYearSubscriptionCount' => number_format($currentYearSubscriptionCount),
        'dailyTransactionSum' => number_format($dailyTransactionSum, 2), // 2 decimal places
        'monthlyTransactionSum' => number_format($monthlyTransactionSum, 2), // 2 decimal places
        'yearlyTransactionSum' => number_format($yearlyTransactionSum, 2), // 2 decimal places
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



}
