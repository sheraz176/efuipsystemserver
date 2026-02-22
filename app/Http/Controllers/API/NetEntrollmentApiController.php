<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\Recusivefailed;
use App\Models\Subscription\FailedSubscription;
use App\Models\Refund\RefundedCustomer;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade;
use Carbon\Carbon;
use App\Models\RecusiveChargingData;
use App\Models\Unsubscription\CustomerUnSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NetEntrollmentApiController extends Controller
{


     public function dailyCount(Request $request)
    {
        // Date lo (default = today)
        $date = $request->query('date', Carbon::today()->toDateString());

        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

        $count = DB::table('customer_subscriptions')
            ->where('policy_status', 1)
            ->whereBetween('recursive_charging_date', [$start, $end])
            ->whereIn('transaction_amount', [1, 2, 10, 12, 200, 299, 163])
            ->count();



        return response()->json([
            'status' => true,
            'date' => $date,
            'total_count' => $count
        ]);
    }


  public function dailyCount2ndloop(Request $request)
    {
        // Date lo (default = today)
        $date = $request->query('date', Carbon::today()->toDateString());

        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

      $data = DB::table('recusive_charging_data')
         ->where('looping', '2nd_loop')
        ->whereBetween('created_at', [$start, $end])
        ->selectRaw('COUNT(*) as total_count, SUM(amount) as total_amount')
        ->first();

        $count = DB::table('Recusive_failed')
            ->where('status', '0')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        return response()->json([
            'status' => true,
            'date' => $date,
            'pending_count' => $count,
             'Success_count' => $data->total_count ?? 0,
             'total_amount' => $data->total_amount ?? 0
        ]);
    }

public function dailyCount3rdloop(Request $request)
    {
        // Date lo (default = today)
        $date = $request->query('date', Carbon::today()->toDateString());

        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

      $data = DB::table('recusive_charging_data')
         ->where('looping', '3rd_loop')
        ->whereBetween('created_at', [$start, $end])
        ->selectRaw('COUNT(*) as total_count, SUM(amount) as total_amount')
        ->first();

        $count = DB::table('Recusive_failed')
               ->where('looping', '2nd_loop')
             ->where('duration', 30)
            ->where('status', '1')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        return response()->json([
            'status' => true,
            'date' => $date,
            'pending_count' => $count,
             'Success_count' => $data->total_count ?? 0,
             'total_amount' => $data->total_amount ?? 0
        ]);
    }



    public function NetEnrollment(Request $request)
    {
        // Check if both startDate and endDate are provided
        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

            // Build the query
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
            ->where('customer_subscriptions.policy_status', '=', '1') // Eager load related models
            ->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);

            // Fetch the data
            $data = $query->get();

            // Prepare the data with headers
            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    'Subscription ID' => $item->subscription_id,
                    'Customer MSISDN' => $item->subscriber_msisdn,
                    'Plan Name' => $item->plan_name,
                    'Product Name' => $item->product_name,
                    'Amount' => $item->transaction_amount,
                    'Duration' => $item->product_duration,
                    'Company Name' => $item->company_name,
                    'Agent ID' => $item->sales_agent,
                    'Transaction ID' => $item->cps_transaction_id,
                    'Reference ID' => $item->referenceId,
                    'Next Charging Date' => $item->recursive_charging_date,
                    'Subscription Date' => $item->subscription_time,
                    'Free Look Period' => $item->grace_period_time,
                ];
            }

            $response = [
                'status' => 'Success',
                'message' => 'Your Net Enrollment Get Successfully',
                'NetEnrollment' => $rows,
            ];

              // Logs
              Log::channel('net_entrollment_api')->info('Net Entrollment Api.',[
                'response-data' => 'Your Net Enrollment Get Successfully',
                ]);

            return response()->json($response, 200);
        } else {
            // Return a response indicating that the date range is required
            $response = [
                'status' => 'Error',
                'message' => 'Start date and end date are required to fetch data.',
            ];

            return response()->json($response, 400);
        }
    }


 public function sub(Request $request)
    {
        // Check if both startDate and endDate are provided
        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

            // Build the query
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
            ->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);

            // Fetch the data
            $data = $query->get();

            // Prepare the data with headers
            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    'Subscription ID' => $item->subscription_id,
                    'Customer MSISDN' => $item->subscriber_msisdn,
                    'Plan Name' => $item->plan_name,
                    'Product Name' => $item->product_name,
                    'Amount' => $item->transaction_amount,
                    'Duration' => $item->product_duration,
                    'Company Name' => $item->company_name,
                    'Agent ID' => $item->sales_agent,
                    'Transaction ID' => $item->cps_transaction_id,
                    'Reference ID' => $item->referenceId,
                    'Next Charging Date' => $item->recursive_charging_date,
                    'Subscription Date' => $item->subscription_time,
                    'Free Look Period' => $item->grace_period_time,
                ];
            }

            $response = [
                'status' => 'Success',
                'message' => 'Your Sub Get Successfully',
                'Sub' => $rows,
            ];

              // Logs
              Log::channel('net_entrollment_api')->info('sub Api.',[
                'response-data' => 'Your sub Get Successfully',
                ]);

            return response()->json($response, 200);
        } else {
            // Return a response indicating that the date range is required
            $response = [
                'status' => 'Error',
                'message' => 'Start date and end date are required to fetch data.',
            ];

            return response()->json($response, 400);
        }
    }



    public function ActiveSubscription(Request $request)
    {
        // Check if both startDate and endDate are provided
        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

            // Build the query
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
            ->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);

            // Fetch the data
            $data = $query->get();

            // Prepare the data with headers
            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    'Subscription ID' => $item->subscription_id,
                    'Customer MSISDN' => $item->subscriber_msisdn,
                    'Plan Name' => $item->plan_name,
                    'Product Name' => $item->product_name,
                    'Amount' => $item->transaction_amount,
                    'Duration' => $item->product_duration,
                    'Company Name' => $item->company_name,
                    'Agent ID' => $item->sales_agent,
                    'Transaction ID' => $item->cps_transaction_id,
                    'Reference ID' => $item->referenceId,
                    'Next Charging Date' => $item->recursive_charging_date,
                    'Subscription Date' => $item->subscription_time,
                    'Free Look Period' => $item->grace_period_time,
                     'Policy Status' => $item->policy_status,

                ];
            }

            $response = [
                'status' => 'Success',
                'message' => 'Your Active Total Subscription  Get Successfully',
                'TotalSubscription' => $rows,
            ];

              // Logs
              Log::channel('net_entrollment_api')->info('Active Total Subscription  Api.',[
                'response-data' => 'Your Total Active Total Subscription Get Successfully',
                ]);

            return response()->json($response, 200);
        } else {
            // Return a response indicating that the date range is required
            $response = [
                'status' => 'Error',
                'message' => 'Start date and end date are required to fetch data.',
            ];

            return response()->json($response, 400);
        }
    }


    public function RefundedTransaction(Request $request)
    {
        // Check if both startDate and endDate are provided
        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');


              // dd($request->all());
             $query = RefundedCustomer::select(
               'refunded_customers.refund_id as refund_id',
               'customer_subscriptions.subscriber_msisdn',
               'customer_subscriptions.transaction_amount',
               'customer_subscriptions.cps_transaction_id',
               'customer_subscriptions.referenceId',
                'refunded_customers.transaction_id',
                'refunded_customers.reference_id',
                'refunded_customers.refunded_by',
                'refunded_customers.refunded_time',
                'plans.plan_name',
                'products.product_name',
                'company_profiles.company_name',
                'refunded_customers.medium',
                'customer_subscriptions.subscription_time',
             )
            ->join('customer_subscriptions', 'refunded_customers.subscription_id', '=', 'customer_subscriptions.subscription_id')
            ->leftJoin('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
            ->leftJoin('products', 'customer_subscriptions.productId', '=', 'products.product_id')
            ->leftjoin('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')// Assuming you pass refunded_id as a parameter
            ->whereDate('refunded_customers.refunded_time', '>=', $startDate)
            ->whereDate('refunded_customers.refunded_time', '<=', $endDate);


            // Fetch the data
            $data = $query->get();

            // Prepare the data with headers
            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    'Refunded ID' => $item->refund_id,
                    'Customer MSISDN' => $item->subscriber_msisdn,
                    'Plan Name' => $item->plan_name,
                    'Product Name' => $item->product_name,
                    'Amount' => $item->transaction_amount,
                    'Company Name' => $item->company_name,
                    'Transaction ID' => $item->cps_transaction_id,
                    'Reference ID' => $item->referenceId,
                    'Medium' => $item->medium,
                    'Subscription Date' => $item->subscription_time,
                    'UnSubscriotion Date' => $item->refunded_time,
                ];
            }

            $response = [
                'status' => 'Success',
                'message' => 'Your Refunded Transaction  Get Successfully',
                'RefundedTransaction' => $rows,
            ];

              // Logs
              Log::channel('net_entrollment_api')->info('Refunded Transaction  Api.',[
                'response-data' => 'Your Refunded Transaction Get Successfully',
                ]);

            return response()->json($response, 200);
        } else {
            // Return a response indicating that the date range is required
            $response = [
                'status' => 'Error',
                'message' => 'Start date and end date are required to fetch data.',
            ];

            return response()->json($response, 400);
        }
    }

  public function recusiveCharging(Request $request)
{
    // Check if date filter is present
    if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        // Build query
        $query = RecusiveChargingData::select([
            'recusive_charging_data.*',
            'plans.plan_name',
            'products.product_name',
        ])
        ->join('plans', 'recusive_charging_data.plan_id', '=', 'plans.plan_id')
        ->join('products', 'recusive_charging_data.product_id', '=', 'products.product_id')
        ->with(['plan', 'product'])
        ->where('cps_response','Process service request successfully.')
        ->whereDate('recusive_charging_data.created_at', '>=', $startDate)
        ->whereDate('recusive_charging_data.created_at', '<=', $endDate);

        $data = $query->get();

        // Prepare formatted rows
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                'Subscription ID' => $item->subscription_id,
                'Customer MSISDN' => $item->customer_msisdn,
                'Plan Name' => $item->plan_name,
                'Product Name' => $item->product_name,
                'Transaction ID' => $item->tid,
                'Reference ID' => $item->reference_id,
                'Amount' => $item->amount,
                'CPS Response' => $item->cps_response,
                'Next Charging Date' => $item->charging_date,
                'Duration' => $item->duration,
                'Created At' => $item->created_at,
            ];
        }

        $response = [
            'status' => 'Success',
            'message' => 'Recursive Charging Data Fetched Successfully',
            'RecursiveChargingData' => $rows,
        ];

        // Logging
        Log::channel('net_entrollment_api')->info('Recursive Charging API.', [
            'response-data' => 'Recursive Charging Data Fetched Successfully',
        ]);

        return response()->json($response, 200);
    } else {
        return response()->json([
            'status' => 'Error',
            'message' => 'Date filter is required to fetch data.',
        ], 400);
    }
}

public function DailyNetEnrollment(Request $request)
{
    $today = now()->toDateString(); // YYYY-MM-DD

    $data = CustomerSubscription::select([
            'customer_subscriptions.*',
            'plans.plan_name',
            'products.product_name',
            'company_profiles.company_name',
        ])
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile'])
        ->where('customer_subscriptions.policy_status', 1)
        ->whereDate('customer_subscriptions.subscription_time', $today)
        ->get();

    $rows = [];
    foreach ($data as $item) {
        $rows[] = [
            'Subscription ID'     => $item->subscription_id,
            'Customer MSISDN'     => $item->subscriber_msisdn,
            'Plan Name'           => $item->plan_name,
            'Product Name'        => $item->product_name,
            'Amount'              => $item->transaction_amount,
            'Duration'            => $item->product_duration,
            'Company Name'        => $item->company_name,
            'Agent ID'            => $item->sales_agent,
            'Transaction ID'      => $item->cps_transaction_id,
            'Reference ID'        => $item->referenceId,
            'Next Charging Date'  => $item->recursive_charging_date,
            'Subscription Date'   => $item->subscription_time,
            'Free Look Period'    => $item->grace_period_time,
        ];
    }

    Log::channel('net_entrollment_api')->info('Today Net Enrollment API', [
        'date' => $today,
        'total_records' => count($rows)
    ]);

    return response()->json([
        'status' => 'Success',
        'message' => 'Today Net Enrollment fetched successfully',
        'NetEnrollment' => $rows,
    ], 200);
}
public function todayCancelled()
{
    $today = now()->toDateString(); // YYYY-MM-DD

    $data = CustomerUnSubscription::select([
            'unsubscriptions.*',
            'customer_subscriptions.subscriber_msisdn',
            'customer_subscriptions.transaction_amount',
            'customer_subscriptions.cps_transaction_id',
            'customer_subscriptions.referenceId',
            'customer_subscriptions.subscription_time',
            'plans.plan_name',
            'products.product_name',
            'company_profiles.company_name',
        ])
        ->join('customer_subscriptions', 'unsubscriptions.subscription_id', '=', 'customer_subscriptions.subscription_id')
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->whereDate('unsubscriptions.unsubscription_datetime', $today)
        ->get();

    $rows = [];
    foreach ($data as $item) {
        $rows[] = [
            'Subscription ID'     => $item->subscription_id,
            'Customer MSISDN'     => $item->subscriber_msisdn,
            'Plan Name'           => $item->plan_name,
            'Product Name'        => $item->product_name,
            'Amount'              => $item->transaction_amount,
            'Company Name'        => $item->company_name,
            'Transaction ID'      => $item->cps_transaction_id,
            'Reference ID'        => $item->referenceId,
            'Subscription Date'   => $item->subscription_time,
            'Unsubscription Date' => $item->unsubscription_datetime,
        ];
    }

    Log::channel('net_entrollment_api')->info('Today Unsubscription API', [
        'date' => $today,
        'total_records' => count($rows),
    ]);

    return response()->json([
        'status' => 'Success',
        'message' => 'Today Unsubscription fetched successfully',
        'TodayCancelled' => $rows,
    ], 200);
}





}
