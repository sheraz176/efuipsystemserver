<?php

namespace App\Http\Controllers\SuperAdmin;

use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;
use App\Models\Company\CompanyProfile;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\RecusiveChargingData;


class SuperAdminReports extends Controller
{
    public function index()
    {
        return view('superadmin.completesales');
    }

    public function getData(Request $request)
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
        ->with(['plan', 'product', 'companyProfile'])
        ->where('customer_subscriptions.policy_status', '=', '1'); // Eager load related models

         if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
             $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
             $endDate = $dateRange[1];
             $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
             ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
          }
        return DataTables::eloquent($query)->toJson();

    }

    public function failed_transactions()
    {
        return view('superadmin.completefailed');
    }

    public function getFailedData(Request $request)
    {


        $query = FailedSubscription::select([
            'insufficient_balance_customers.*',
            'plans.plan_name',
            'products.product_name',
            'company_profiles.company_name',
            'tele_sales_agents.username',
            ])
            ->join('plans', 'insufficient_balance_customers.planId', '=', 'plans.plan_id')
             ->join('products', 'insufficient_balance_customers.product_id', '=', 'products.product_id')
             ->join('company_profiles', 'insufficient_balance_customers.company_id', '=', 'company_profiles.id')
             ->join('tele_sales_agents', 'insufficient_balance_customers.agent_id', '=', 'tele_sales_agents.agent_id')
             ->with(['plan','product','companyProfile','teleSalesAgent']);

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
            $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
            ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);
            // $query->whereBetween('insufficient_balance_customers.sale_request_time', [$startDate, $endDate]);

        }


        return DataTables::eloquent($query)->toJson();
    }


    public function companies_reports()
    {
        $companies = CompanyProfile::all();
        return view('superadmin.company.companywisereports',compact('companies'));
    }

    public function getDataCompany(Request $request)
{
    // $a=$request->input('dateFilter');
    // dd($a);
    $query = CustomerSubscription::select([
        'customer_subscriptions.*', // Select all columns from customer_subscriptions table
        'plans.plan_name', // Select the plan_name column from the plans table
        'products.product_name', // Select the product_name column from the products table
        'company_profiles.company_name', // Select the company_name column from the company_profiles table
    ])
    ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
    ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
    ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
    ->with(['plan', 'product', 'companyProfile'])
    ->where('customer_subscriptions.policy_status', '=', '1'); // Eager load related models
    // Apply filters if provided
    if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
        $query->where('customer_subscriptions.company_id', $request->input('companyFilter'));
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

        $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
        ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
    }
    return DataTables::eloquent($query)->toJson();

}


public function companies_failed_reports()
{
    $companies = CompanyProfile::all();
    return view('superadmin.company.companyfailedreports',compact('companies'));
}

public function companies_failed_data(Request $request)
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
        $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
        ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);
        // $query->whereBetween('insufficient_balance_customers.sale_request_time', [$startDate, $endDate]);
    }
    return DataTables::eloquent($query)->toJson();
}

public function companies_unsubscribed_reports()
{
    $companies = CompanyProfile::all();
    return view('superadmin.company.companycancelledreports',compact('companies'));
}

public function companies_cancelled_data(Request $request)
{
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
        $query->whereDate('unsubscriptions.unsubscription_datetime', '>=', $startDate)
        ->whereDate('unsubscriptions.unsubscription_datetime', '<=', $endDate);
        // $query->whereBetween('unsubscriptions.unsubscription_datetime', [$startDate, $endDate]);

        $query->addSelect([
            \DB::raw('TIMESTAMPDIFF(SECOND, customer_subscriptions.subscription_time, unsubscriptions.unsubscription_datetime) as subscription_duration')
        ]);
    }

    return DataTables::eloquent($query)->toJson();


}


public function complete_active_subscription()
{

    return view('superadmin.completeactivecustomers');
}


public function get_active_subscription_data(Request $request)
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
        ->with(['plan', 'product', 'companyProfile']); // Eager load related models

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
            $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
        }
        return DataTables::eloquent($query)->toJson();

    }

    public function recusive_charging_data_index()
    {

    return view('superadmin.recusive-charging.index');
    }


    public function get_recusive_charging_data(Request $request)
    {

        // RecusiveChargingData

        $query = RecusiveChargingData::select([
            'recusive_charging_data.*', // Select all columns from recusive_charging_data table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
        ])
        ->join('plans', 'recusive_charging_data.plan_id', '=', 'plans.plan_id')
        ->join('products', 'recusive_charging_data.product_id', '=', 'products.product_id')
        ->orderBy('created_at', 'desc')
        ->with(['plan', 'product']); // Eager load related models


        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
            $query->whereDate('recusive_charging_data.created_at', '>=', $startDate)
            ->whereDate('recusive_charging_data.created_at', '<=', $endDate);
        }
        return DataTables::eloquent($query)->toJson();

    }


}
