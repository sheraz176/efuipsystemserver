<?php

namespace App\Http\Controllers\CompanyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Refund\RefundedCustomer;
use App\Models\Subscription\CustomerSubscription;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;

        // Count of today's subscriptions
        $todaySubscriptionCount = CustomerSubscription::where('company_id', $companyId)
            ->whereDate('created_at', Carbon::today())
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

           // CustomerSubscription Line Chart Start
            $Customer_Subscriptions = CustomerSubscription::where('subscription_time', '>=', Carbon::now()->subDays(30))
             ->where('policy_status', '1')->where('company_id', $companyId)->get();
             $CustomerSubscriptionData = [];
            foreach ($Customer_Subscriptions as $CustomerSubscription) {
                $date = $CustomerSubscription->created_at->toDateString();

                if (!isset($CustomerSubscriptionData[$date])) {
                    $CustomerSubscriptionData[$date] = 0;
                }

                $CustomerSubscriptionData[$date]++;
            }
            // CustomerSubscription Line Chart End

            // RefundedCustomer Line Chart Start
            $Refunded_Customers = RefundedCustomer::where('unsubscriptions.unsubscription_datetime', '>=', Carbon::now()->subDays(30))
            ->join('customer_subscriptions', 'refunded_customers.subscription_id', '=', 'customer_subscriptions.subscription_id')
            ->join('unsubscriptions', 'customer_subscriptions.subscription_id', '=', 'unsubscriptions.subscription_id')
            ->where('customer_subscriptions.company_id', '=', $companyId)
            ->get();
            $RefundedCustomersData = [];
            foreach ($Refunded_Customers as $RefundedCustomer) {
                $date = $RefundedCustomer->created_at->toDateString();

                if (!isset($RefundedCustomersData[$date])) {
                    $RefundedCustomersData[$date] = 0;
                }

                $RefundedCustomersData[$date]++;
            }
         // RefundedCustomer Line Chart End

        return view('company_manager.dashboard', [
            'todaySubscriptionCount' => $todaySubscriptionCount,
            'currentMonthSubscriptionCount' => $currentMonthSubscriptionCount,
            'currentYearSubscriptionCount' => $currentYearSubscriptionCount,
            'dailyTransactionSum' => $dailyTransactionSum,
            'monthlyTransactionSum' => $monthlyTransactionSum,
            'yearlyTransactionSum' => $yearlyTransactionSum,
            'CustomerSubscriptionData' =>$CustomerSubscriptionData,
            'RefundedCustomersData' =>$RefundedCustomersData,
        ]);
    }
}
