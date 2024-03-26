<?php

namespace App\Http\Controllers\SuperAdmin\AgentsReports;

use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeleSalesAgent;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;

class AgentReportsController extends Controller
{
    public function agents_Subscriptions()
    {
        $agents = TeleSalesAgent::all();
        return view('superadmin.agent.agentwisereports',compact('agents'));
    }


    public function agents_get_data(Request $request)
    {
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile']) // Eager load related models
        ->where('customer_subscriptions.policy_status', '=', '1'); // Eager load related models

        // Apply filters if provided
        if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
            $query->where('customer_subscriptions.sales_agent', $request->input('companyFilter'));
        }

        // if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        //     $dateRange = explode(' to ', $request->input('dateFilter'));
        //     $startDate = date('Y-m-d H:i:s', strtotime($dateRange[0] . ' 00:00:00'));
        //     $endDate = date('Y-m-d H:i:s', strtotime($dateRange[1] . ' 23:59:59'));

        //     $query->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
        // }

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
        }

        return DataTables::eloquent($query)->toJson();
    }


    public function agents_sales_request()
    {
        $agents = TeleSalesAgent::all();
        return view('superadmin.agent.agentsalerequest',compact('agents'));
    }

    public function agents_sales_data(Request $request)
    {
        $query = FailedSubscription::select([
            'insufficient_balance_customers.request_id', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.transactionId', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.referenceId', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.timeStamp', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.accountNumber', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.resultDesc', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.failedReason', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.amount', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->join('plans', 'insufficient_balance_customers.planId', '=', 'plans.plan_id')
        ->join('products', 'insufficient_balance_customers.product_id', '=', 'products.product_id')
        ->join('company_profiles', 'insufficient_balance_customers.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile']); // Eager load related models


        // Apply filters if provided
        if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
            $query->where('insufficient_balance_customers.agent_id', $request->input('companyFilter'));
        }

        // if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        //     $dateRange = explode(' to ', $request->input('dateFilter'));
        //     $startDate = date('Y-m-d H:i:s', strtotime($dateRange[0] . ' 00:00:00'));
        //     $endDate = date('Y-m-d H:i:s', strtotime($dateRange[1] . ' 23:59:59'));

        //     $query->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
        // }

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereBetween('insufficient_balance_customers.sale_request_time', [$startDate, $endDate]);
        }

        return DataTables::eloquent($query)->toJson();
    }


}
