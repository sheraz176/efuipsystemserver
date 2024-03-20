<?php

namespace App\Http\Controllers\CompanyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return view('company_manager.dashboard', [
            'todaySubscriptionCount' => $todaySubscriptionCount,
            'currentMonthSubscriptionCount' => $currentMonthSubscriptionCount,
            'currentYearSubscriptionCount' => $currentYearSubscriptionCount,
            'dailyTransactionSum' => $dailyTransactionSum,
            'monthlyTransactionSum' => $monthlyTransactionSum,
            'yearlyTransactionSum' => $yearlyTransactionSum,
        ]);
    }
}
