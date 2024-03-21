<?php

namespace App\Http\Controllers\CompanyManager;
use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;
use App\Models\Company\CompanyProfile;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\RecusiveChargingData;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\TeleSalesAgent;
use App\Models\Refund\RefundedCustomer;


class CompanyManagerReportController extends Controller
{
    public function complete_sales_index()
    {
        return view('company_manager.reports.completesales');
    }
    public function getData(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        //   dd($companyId);
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->where('customer_subscriptions.company_id', '=', $companyId)
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile']); // Eager load related models

         if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
             $dateRange = explode(' to ', $request->input('dateFilter'));
             $startDate = $dateRange[0];
             $endDate = $dateRange[1];

             $query->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
         }

        $data = $query->get();

        // dd($data);

        return DataTables::of($data)->make(true);
    }

    public function failed_transactions()
    {
        return view('company_manager.reports.completefailed');
    }

    public function getFailedData(Request $request)
    {

        $companyId = Auth::guard('company_manager')->user()->company_id;
        $query = FailedSubscription::select([
            'insufficient_balance_customers.*',
            'plans.plan_name',
            'products.product_name',
            'company_profiles.company_name',
            'tele_sales_agents.username',
            ])
            ->where('insufficient_balance_customers.company_id', '=', $companyId)
            ->join('plans', 'insufficient_balance_customers.planId', '=', 'plans.plan_id')
             ->join('products', 'insufficient_balance_customers.product_id', '=', 'products.product_id')
             ->join('company_profiles', 'insufficient_balance_customers.company_id', '=', 'company_profiles.id')
             ->join('tele_sales_agents', 'insufficient_balance_customers.agent_id', '=', 'tele_sales_agents.agent_id')
             ->with(['plan','product','companyProfile','teleSalesAgent']);

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereBetween('insufficient_balance_customers.sale_request_time', [$startDate, $endDate]);
        }

        // $query = $query->get();

        // return DataTables::of($query)->make(true);

        return DataTables::eloquent($query)->toJson();
    }

    public function complete_active_subscription()
{

    return view('company_manager.reports.completeactivecustomers');
}


public function activecustomerdataget(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->where('customer_subscriptions.company_id', '=', $companyId)
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile'])
        ->where('customer_subscriptions.policy_status', '=', '1'); // Eager load related models

         if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
             $dateRange = explode(' to ', $request->input('dateFilter'));
             $startDate = $dateRange[0];
             $endDate = $dateRange[1];

             $query->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
         }

        $data = $query->get();

        return DataTables::of($data)->make(true);
    }

    public function companies_unsubscribed_reports()
   {
    $companies = CompanyProfile::all();
    return view('company_manager.reports.companycancelledreports',compact('companies'));
   }

   public function companies_cancelled_data(Request $request)
   {
    $companyId = Auth::guard('company_manager')->user()->company_id;
    $query = CustomerUnSubscription::select([
        'unsubscriptions.unsubscription_id',
        'customer_subscriptions.subscriber_msisdn',
        'plans.plan_name',
        'products.product_name',
        'customer_subscriptions.transaction_amount',
        'customer_subscriptions.cps_transaction_id',
        'customer_subscriptions.referenceId',
        'customer_subscriptions.subscription_time',
        'unsubscriptions.unsubscription_datetime',
        'unsubscriptions.medium',
        'company_profiles.company_name',
    ])
    ->where('customer_subscriptions.company_id', '=', $companyId)
    ->join('customer_subscriptions', 'customer_subscriptions.subscription_id', '=', 'unsubscriptions.subscription_id')
    ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
    ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
    ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id');

    // Apply filters if provided
    if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
        $query->where('company_profiles.company_id', $request->input('companyFilter'));
    }

    if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        $query->whereBetween('unsubscriptions.unsubscription_datetime', [$startDate, $endDate]);

        $query->addSelect([
            \DB::raw('TIMESTAMPDIFF(SECOND, customer_subscriptions.subscription_time, unsubscriptions.unsubscription_datetime) as subscription_duration')
        ]);
    }

    $data = $query->get();
    return DataTables::of($data)->make(true);

    }

    public function refundReports(Request $request)
    {
        $companies = CompanyProfile::all();
        return view('company_manager.reports.refundreport', compact('companies'));
    }

    public function getRefundedData(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $refundData = RefundedCustomer::select(
            'refunded_customers.refund_id as refund_id',
            'customer_subscriptions.subscriber_msisdn',
            'customer_subscriptions.transaction_amount',
            'unsubscriptions.unsubscription_datetime',
            'refunded_customers.transaction_id',
            'refunded_customers.reference_id',
            'refunded_customers.refunded_by',
            'plans.plan_name',
            'products.product_name',
            'company_profiles.company_name',
            'refunded_customers.medium'
        )
            ->where('customer_subscriptions.company_id', '=', $companyId)
            ->join('customer_subscriptions', 'refunded_customers.subscription_id', '=', 'customer_subscriptions.subscription_id')
            ->join('unsubscriptions', 'customer_subscriptions.subscription_id', '=', 'unsubscriptions.subscription_id')
            ->leftJoin('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
            ->leftJoin('products', 'customer_subscriptions.productId', '=', 'products.product_id')
            ->leftjoin('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id');// Assuming you pass refunded_id as a parameter

            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];

                $refundData->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
            }

            // $refundData = $refundData->get();

            // return DataTables::of($refundData)

            // ->make(true);

            return DataTables::eloquent($refundData)->toJson();
    }

    public function manage_refund_index()
    {
        return view('company_manager.reports.refundtable');
    }

    public function getRefundData(Request $request)
    {
        $todayDate = Carbon::now()->toDateString();
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
            ->where('customer_subscriptions.company_id', '=', $companyId)
            ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
            ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
            ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
            ->with(['plan', 'product', 'companyProfile'])
            ->where('grace_period_time', '>=', $todayDate) // Eager load related models
            ->where('policy_status', '=', 1);

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
        }

        // Add custom search functionality for numeric columns
        if ($request->has('msisdn') && !empty($request->input('msisdn'))) {
            $msisdn = $request->input('msisdn');
            $query->where('customer_subscriptions.subscriber_msisdn', 'like', '%' . $msisdn . '%');
        }

        // Use DataTables for pagination and server-side processing
        return DataTables::eloquent($query)->toJson();
    }

    public function agents_Subscriptions()
    {
        $agents = TeleSalesAgent::all();
        return view('company_manager.reports.agentwisereports',compact('agents'));
    }


    public function agents_get_data(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->where('customer_subscriptions.company_id', '=', $companyId)
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile']); // Eager load related models


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
        return view('company_manager.reports.agentsalerequest',compact('agents'));
    }

    public function agents_sales_data(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
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
        ->where('insufficient_balance_customers.company_id', '=', $companyId)
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

    public function check_agent_status()
    {
        // dd('hi');
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $telesalesAgents = TelesalesAgent::where('company_id', $companyId)->get();
        return view('company_manager.agent-status', compact('telesalesAgents'));
    }

}
