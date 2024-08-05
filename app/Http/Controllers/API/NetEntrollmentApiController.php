<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
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

}
