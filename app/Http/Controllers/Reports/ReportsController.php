<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Subscription\FailedSubscription;

class ReportsController extends Controller
{
    public function overall_report()
    {

        $agent = session('agent');

        if (!$agent) {
            
            return redirect()->back()->withInput()->withErrors(['login' => 'Session Expired Kindly Re-login']);
        }

        $agentId = $agent->agent_id;
        $currentMonthTotal = CustomerSubscription::where('sales_agent', $agentId)
            ->whereMonth('subscription_time', Carbon::now()->month)
            ->sum('transaction_amount');

       // $overallSales = CustomerSubscription::where('sales_agent', $agentId)->sum('transaction_amount');

        $result = CustomerSubscription::where('sales_agent', $agentId)
            ->selectRaw('SUM(transaction_amount) as total_amount, COUNT(*) as total_count')
            ->first();

        $overallSales = $result->total_amount;
        $TotalSalesCount = $result->total_count;

        
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


        
        $totalFailedTransactionsCount = FailedSubscription::where('agent_id', $agentId)->count();
        $insufficientBalance = FailedSubscription::where('agent_id', $agentId)->where('resultCode', '2009')->count();

        $monthlyFailedTransactionsCount = FailedSubscription::where('agent_id', $agentId)
        ->whereMonth('timeStamp', now()->month)
        ->count();

        $dailyFailedTransactionsCount = FailedSubscription::where('agent_id', $agentId)
        ->whereDate('timeStamp', today())
        ->count();


             return view('agent.overallreport', compact('currentMonthTotal', 'currentYearTotal', 'currentDayTotal','currentMonthTotalCount','currentDayTotalCount',
              'overallSales','TotalSalesCount','totalFailedTransactionsCount','insufficientBalance','monthlyFailedTransactionsCount','dailyFailedTransactionsCount','agent'));
           

        //return view('agent.overallreport');
    }
}
