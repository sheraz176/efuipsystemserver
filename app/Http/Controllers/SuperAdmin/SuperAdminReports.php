<?php

namespace App\Http\Controllers\SuperAdmin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;
use App\Models\Company\CompanyProfile;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\RecusiveChargingData;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SuperAdminReports extends Controller
{
    public function index()
    {
        return view('superadmin.completesales');
    }

    public function getData(Request $request)
{
    if ($request->ajax()) {


        // Start building the query
        $query = CustomerSubscription::select('*')->where('policy_status' , '1');

        // Apply date filters if provided
              // Apply date filters if provided
        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
            $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
                  ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
        }

        return Datatables::of($query)->addIndexColumn()

            ->addColumn('company_name', function($data){
                return $data->company->company_name;
            })
            ->addColumn('plan_name', function($data){
                return $data->plan->plan_name;
            })
            ->addColumn('product_name', function($data){
                return $data->products->product_name;
            })

            ->addColumn('consistent_provider', function($data){

                  $data_count = count($data->interested_customers);
                //   return  $data_count;
                if($data_count > 0) {
                    return "(DTMF)." . $data->interested_customers[$data_count - 1]->consistent_provider . "";
                } else {
                    return "";
                }
             })



            ->rawColumns(['company_name', 'plan_name', 'product_name','consistent_provider'])
            ->make(true);
    }
}




    public function failed_transactions()
    {
        return view('superadmin.completefailed');
    }

    public function getFailedData(Request $request)
    {


        if ($request->ajax()) {
            // Start building the query
            $query = FailedSubscription::select('*');

            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
                      ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);
            }

            return Datatables::of($query)->addIndexColumn()
                ->addColumn('plan_name', function ($data) {
                    return $data->plan->plan_name;
                })
                ->addColumn('product_name', function ($data) {
                    return $data->product->product_name;
                })
                ->addColumn('company_name', function ($data) {
                    return $data->company->company_name;
                })
                ->addColumn('username', function ($data) {
                    if ($data->teleSalesAgent) {
                        return $data->teleSalesAgent->username;
                    }
                    return 'N/A'; // Or any other default value you prefer
                })
                ->rawColumns(['plan_name', 'product_name', 'company_name', 'username'])
                ->make(true);
        }


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

    if ($request->ajax()) {
        // Start building the query
        $query = CustomerUnSubscription::with(['customer_subscription.company', 'customer_subscription.plan', 'customer_subscription.products'])
            ->select('*');

        // Apply company filter if provided
        if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
            $query->whereHas('customer_subscription.company', function ($q) use ($request) {
                $q->where('id', $request->input('companyFilter'));
            });
        }

    if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];
        $query->whereDate('unsubscriptions.unsubscription_datetime', '>=', $startDate)
        ->whereDate('unsubscriptions.unsubscription_datetime', '<=', $endDate);

    }

        return Datatables::of($query)->addIndexColumn()
            ->addColumn('subscriber_msisdn', function ($data) {
                return $data->customer_subscription->subscriber_msisdn;
            })
            ->addColumn('transaction_amount', function ($data) {
                return $data->customer_subscription->transaction_amount;
            })
            ->addColumn('plan_name', function ($data) {
                return $data->customer_subscription->plan->plan_name;
            })
            ->addColumn('product_name', function ($data) {
                return $data->customer_subscription->products->product_name;
            })
            ->addColumn('company_name', function ($data) {
                return $data->customer_subscription->company->company_name;
            })
            ->addColumn('cps_transaction_id', function ($data) {
                return $data->customer_subscription->cps_transaction_id;
            })
            ->addColumn('referenceId', function ($data) {
                return $data->customer_subscription->referenceId;
            })
            ->addColumn('subscription_time', function ($data) {
                return $data->customer_subscription->subscription_time;
            })
            ->rawColumns(['subscriber_msisdn','cps_transaction_id', 'transaction_amount', 'plan_name', 'product_name', 'company_name', 'subscription_time'])
            ->make(true);
    }



}


public function complete_active_subscription()
{

    return view('superadmin.completeactivecustomers');
}


public function get_active_subscription_data(Request $request)
    {

    if ($request->ajax()) {
          // Start building the query
          $query = CustomerSubscription::select('*');
                // Apply date filters if provided
          if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
              $dateRange = explode(' to ', $request->input('dateFilter'));
              $startDate = $dateRange[0];
              $endDate = $dateRange[1];
              $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
                    ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
          }
          return Datatables::of($query)->addIndexColumn()

              ->addColumn('company_name', function($data){
                  return $data->company->company_name;
              })
              ->addColumn('plan_name', function($data){
                  return $data->plan->plan_name;
              })
              ->addColumn('product_name', function($data){
                  return $data->products->product_name;
              })

              ->rawColumns(['company_name', 'plan_name', 'product_name'])
              ->make(true);
      }

    }

    public function recusive_charging_data_index()
    {

    return view('superadmin.recusive-charging.index');
    }


    public function get_recusive_charging_data(Request $request)
    {
        // dd('hi');
        // RecusiveChargingData
        if ($request->ajax()) {
            // Start building the query
            $query = RecusiveChargingData::select('*');
            // Apply date filters if provided
            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('recusive_charging_data.created_at', '>=', $startDate)
                ->whereDate('recusive_charging_data.created_at', '<=', $endDate);
            }
            return Datatables::of($query)->addIndexColumn()
                ->addColumn('plan_name', function($data){
                    return $data->plans->plan_name;
                })
                ->addColumn('product_name', function($data){
                    return $data->product->product_name;
                })
                ->rawColumns(['plan_name','product_name'])
                ->make(true);
        }

    }


}
