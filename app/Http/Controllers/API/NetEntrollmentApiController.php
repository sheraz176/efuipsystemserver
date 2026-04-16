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
use App\Models\SMSMsisdn;
use App\Models\Plans\ProductModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;


class NetEntrollmentApiController extends Controller
{


    public function sub(Request $request)
{
    if ($request->has('startDate') && $request->has('endDate')) {

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $query = CustomerSubscription::select([
                'customer_subscriptions.*',
                'plans.plan_name',
                'products.product_name',
                'company_profiles.company_name',
            ])
            ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
            ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
            ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
            ->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);

        return response()->stream(function () use ($query, $startDate, $endDate) {

            echo '{"status":"Success","message":"Streaming Data","Sub":[';

            $first = true;

            // ✅ 1. DB DATA STREAM
            foreach ($query->cursor() as $item) {

                if (!$first) {
                    echo ',';
                }

                echo json_encode([
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
                    'Policy_Status' => $item->policy_status == 1 ? 'Active' : 'In Active',
                ]);

                $first = false;

                ob_flush();
                flush();
            }

            // ✅ 2. API CALL (ONLY ONCE)
            try {
                $apiResponse = Http::post('https://jazzcash-health.efulife.com/api/getSubscriptionscashless', [
                    'from_date' => $startDate,
                    'to_date'   => $endDate,
                ]);

                if ($apiResponse->successful()) {
                    $apiData = $apiResponse->json();

                    foreach ($apiData['data'] ?? [] as $item) {

                        if (!$first) {
                            echo ',';
                        }

                        echo json_encode([
                            'Subscription ID' => $item['id'] ?? null,
                            'Customer MSISDN' => $item['customer_name'] ?? null,
                            'Plan Name' => $item['plan_name'] ?? null,
                            'Product Name' => $item['plan_name'] ?? null,
                            'Amount' => $item['amount'] ?? null,
                            'Duration' => $item['payment_frequency'] ?? null,
                            'Company Name' => 'HealthTech Cashless',
                            'Agent ID' => '1',
                            'Transaction ID' => $item['jazzcash_tid'] ?? null,
                            'Reference ID' => $item['jazzcash_reference_id'] ?? null,
                            'Next Charging Date' => $item['next_charging_date'] ?? null,
                            'Subscription Date' => $item['subscription_timestamp'] ?? null,
                            'Free Look Period' => $item['subscription_timestamp'] ?? null,
                            'Policy Status' => isset($item['status'])
    ? ($item['status'] === 'CANCELLED' ? 'In Active'
        : ($item['status'] === 'ACTIVE' ? 'Active' : $item['status']))
    : null,
                        ]);

                        $first = false;

                        ob_flush();
                        flush();
                    }
                }

            } catch (\Exception $e) {
                Log::error('API fetch failed: ' . $e->getMessage());
            }

            echo ']}';

        }, 200, [
            'Content-Type' => 'application/json',
        ]);

    } else {
        return response()->json([
            'status' => 'Error',
            'message' => 'Start date and end date are required'
        ], 400);
    }
}



    public function healthtechUnSub(Request $request)
    {
        // Step 1: Validate the request
        $validator = Validator::make($request->all(), [
            'subscriber_msisdn' => 'required|regex:/^\d{11,12}$/',
            'amount' => 'required|numeric', // Match subscription by transaction_amount
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $subscriber_msisdn = $request->input('subscriber_msisdn');

        // Convert 923XXXXXXXXX to 03XXXXXXXXX
        if (preg_match('/^92\d{10}$/', $subscriber_msisdn)) {
            $subscriber_msisdn = '0' . substr($subscriber_msisdn, 2);
        }

        // Step 2: Fetch active subscriptions
        $subscriptions = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('policy_status', 1)
            ->get();

        if ($subscriptions->isEmpty()) {
            return response()->json([
                'statusCode' => 4004,
                'message' => 'No active subscriptions found for this subscriber.',
            ], 404);
        }

        $amount = $request->input('amount');

        // Step 3: Find subscription matching the transaction_amount
        $subscription = $subscriptions->where('transaction_amount', $amount)->first();

        if (!$subscription) {
            return response()->json([
                'statusCode' => 4004,
                'message' => 'No active subscription found matching the given amount.',
            ], 404);
        }

        // Step 4: Perform non-refundable unsubscription
        CustomerUnSubscription::create([
            'unsubscription_datetime' => now(),
            'medium' => 'Health Tech',
            'subscription_id' => $subscription->subscription_id,
            'refunded_id' => '1', // Not refunded
        ]);

        $subscription->update(['policy_status' => 0]);

        return response()->json([
            'statusCode' => 2001,
            'refund' => 'false',
            'medium' => 'Health Tech',
            'message' => 'Package unsubscribed successfully. You are not eligible for a refund.',
        ]);
    }



    public function HealthTechSubscription(Request $request)
    {
        // ? Validation
        $validator = Validator::make($request->all(), [
            'customer_msisdn'      => 'required|regex:/^\d{11,12}$/',
            'transaction_amount'   => 'required|numeric',
            'jazzcash_tid'         => 'required|string',
            'jazzcash_reference_id' => 'required|string',
            'customer_name'        => 'required|string',
            'customer_cnic'        => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 400,
                'message'    => $validator->errors()
            ], 400);
        }

        // ? Normalize MSISDN
        $subscriber_msisdn = $request->customer_msisdn;

        if (preg_match('/^92\d{10}$/', $subscriber_msisdn)) {
            $subscriber_msisdn = '0' . substr($subscriber_msisdn, 2);
        }

        $transactionAmount = $request->transaction_amount;
        $subscriberCnic    = $request->customer_cnic;

        // ? Fetch Product by Fee
        // ? Fetch Product by Fee but only plan_id 4 or 5
        $product = ProductModel::where('fee', $transactionAmount)
            ->whereIn('plan_id', [4, 5])
            ->first();

        if (!$product) {
            return response()->json([
                'statusCode' => 4001,
                'message' => 'Invalid transaction amount. No product found against this fee.'
            ], 404);
        }

        $planId     = $product->plan_id;
        $productId  = $product->product_id;
        $duration   = $product->duration;

        // ? Check Already Subscribed (Active Only)
        $existingSubscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('plan_id', $planId)
            ->where('policy_status', 1)
            ->first();

        if ($existingSubscription) {

            return response()->json([
                'error' => false,
                'statusCode' => 4000,
                'message' => 'Already subscribed to the plan.',
                'policy_subscription_id' => $existingSubscription->subscription_id,
                'planCode' => $product->product_code,
                'transactionAmount' => $existingSubscription->transaction_amount,
                'subscriber_msisdn' => $existingSubscription->subscriber_msisdn,
                'subscription_time' => $existingSubscription->subscription_time
            ]);
        }

        // ? Date Calculations
        $gracePeriodDays = 14;

        $gracePeriodTime = now()->addDays($gracePeriodDays);
        $recursiveChargingDate = now()->addDays($duration);

        DB::beginTransaction();

        try {

            // ? Create Subscription
            $subscription = CustomerSubscription::create([
                'customer_id'             => '0011' . $subscriber_msisdn,
                'payer_cnic'              => 1,
                'payer_msisdn'            => $subscriber_msisdn,
                'subscriber_cnic'         => $subscriberCnic,
                'subscriber_msisdn'       => $subscriber_msisdn,
                'beneficiary_name'        => 'Need to Filled in Future',
                'beneficiary_msisdn'      => 0,
                'transaction_amount'      => $transactionAmount,
                'transaction_status'      => 1,
                'referenceId'             => $request->jazzcash_reference_id,
                'cps_transaction_id'      => $request->jazzcash_tid,
                'cps_response_text'       => "Service Activated Successfully",
                'product_duration'        => $duration,
                'plan_id'                 => $planId,
                'productId'               => $productId,
                'policy_status'           => 1,
                'pulse'                   => "Health_Tech",
                'api_source'              => "Health Tech System",
                'recursive_charging_date' => $recursiveChargingDate,
                'subscription_time'       => now(),
                'grace_period_time'       => $gracePeriodTime,
                'sales_agent'             => 1,
                'company_id'              => 25
            ]);

            // ? Insert SMS Log
            SMSMsisdn::create([
                'msisdn'     => $subscriber_msisdn,
                'plan_id'    => $planId,
                'product_id' => $productId,
                'status'     => 0
            ]);

            DB::commit();

            // ? Success Response
            return response()->json([
                'error' => false,
                'statusCode' => 2000,
                'message' => 'Customer Subscribed Successfully',
                'policy_subscription_id' => $subscription->subscription_id,
                'Information' => [
                    'subscriber_msisdn' => $subscription->subscriber_msisdn,
                    'transaction_amount' => $subscription->transaction_amount,
                    'transactionStatus' => $subscription->transaction_status,
                    'cpsResponse'       => $subscription->cps_response_text,
                    'planId'            => $subscription->plan_id,
                    'productId'         => $subscription->productId,
                    'planCode'          => $product->product_code,
                    'plan_status'       => $subscription->policy_status,
                    'ApiSource'         => $subscription->api_source,
                ]
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'error' => true,
                'statusCode' => 5000,
                'message' => 'Something went wrong',
                'debug' => $e->getMessage() // remove in production
            ], 500);
        }
    }


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
            Log::channel('net_entrollment_api')->info('Net Entrollment Api.', [
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
        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

            $rows = [];

            // 1?? Fetch DB data
            $dbData = CustomerSubscription::select([
                'customer_subscriptions.*',
                'plans.plan_name',
                'products.product_name',
                'company_profiles.company_name',
            ])
                ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
                ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
                ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
                ->with(['plan', 'product', 'companyProfile'])
                ->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
                ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate)
                ->get();

            foreach ($dbData as $item) {
                $rows[] = [
                    'Subscription ID'      => $item->subscription_id,
                    'Customer MSISDN'      => $item->subscriber_msisdn,
                    'Plan Name'            => $item->plan_name,
                    'Product Name'         => $item->product_name,
                    'Amount'               => $item->transaction_amount,
                    'Duration'             => $item->product_duration,
                    'Company Name'         => $item->company_name,
                    'Agent ID'             => $item->sales_agent,
                    'Transaction ID'       => $item->cps_transaction_id,
                    'Reference ID'         => $item->referenceId,
                    'Next Charging Date'   => $item->recursive_charging_date,
                    'Subscription Date'    => $item->subscription_time,
                    'Free Look Period'     => $item->grace_period_time,
                    'Policy Status'        => $item->policy_status,
                ];
            }

            // 2?? Fetch API data (only product_id and plan_name)

            try {
                $apiResponse = Http::post('https://jazzcash-health.efulife.com/api/getSubscriptionscashless', [
                    'from_date' => $startDate,
                    'to_date'   => $endDate,
                ]);

                if ($apiResponse->successful()) {
                    $apiData = $apiResponse->json();

                    foreach ($apiData['data'] ?? [] as $item) {
                        $rows[] = [
                            'Subscription ID'      => $item['id'] ?? null,
                            'Customer MSISDN'      => $item['customer_name'] ?? null, // or use MSISDN if available
                            'Plan Name'            => $item['plan_name'] ?? null,
                            'Product Name'         => $item['product_id'] ?? null, // map product name if needed
                            'Amount'               => $item['amount'] ?? null,
                            'Duration'             => $item['payment_frequency'] ?? null,
                            'Company Name'         => 'Cashless', // API doesn’t provide company name
                            'Agent ID'             => '1', // API doesn’t provide agent ID
                            'Transaction ID'       => $item['jazzcash_tid'] ?? null, // API doesn’t provide transaction ID
                            'Reference ID'         => $item['jazzcash_reference_id'] ?? null, // API doesn’t provide reference ID
                            'Next Charging Date'   => $item['next_charging_date'] ?? null,
                            'Subscription Date'    => $item['subscription_timestamp'] ?? null,
                            'Free Look Period'     => $item['subscription_timestamp'] ?? null, // API doesn’t provide
               'Policy Status' => isset($item['status'])
    ? ($item['status'] === 'CANCELLED' ? 'In Active'
        : ($item['status'] === 'ACTIVE' ? 'Active' : $item['status']))
    : null,
                        ];
                    }
                } else {
                    Log::error('API call failed', [
                        'status' => $apiResponse->status(),
                        'body' => $apiResponse->body(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('API fetch failed: ' . $e->getMessage());
            }

            // 3?? Return combined data
            $response = [
                'status' => 'Success',
                'message' => 'Active subscriptions fetched successfully from DB and API.',
                'TotalSubscription' => $rows,
            ];

            Log::channel('net_entrollment_api')->info('Active Total Subscription API + DB', [
                'response-count' => count($rows),
            ]);

            return response()->json($response, 200);
        }

        return response()->json([
            'status' => 'Error',
            'message' => 'Start date and end date are required to fetch data.',
        ], 400);
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
                ->leftjoin('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id') // Assuming you pass refunded_id as a parameter
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
            Log::channel('net_entrollment_api')->info('Refunded Transaction  Api.', [
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
                ->where('cps_response', 'Process service request successfully.')
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



    public function FamilyHealthPlan(Request $request)
    {
        try {

            $today = now()->toDateString(); // YYYY-MM-DD

            $data = CustomerSubscription::select([
                'customer_subscriptions.*',
                'plans.plan_name',
                'products.product_name',
                'plans.plan_id',
                'products.product_id',

                'company_profiles.company_name',
            ])
                ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
                ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
                ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
                ->with(['plan', 'product', 'companyProfile'])
                ->whereIn('customer_subscriptions.plan_id', [4, 5])
                ->where('customer_subscriptions.policy_status', 1)
                ->where('customer_subscriptions.subscriber_msisdn', $request->msisdn)
                ->get();

            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    'Subscription ID'     => $item->subscription_id,
                    'Customer MSISDN'     => $item->subscriber_msisdn,
                    'Plan Name'           => $item->plan_name,
                    'Product Name'        => $item->product_name,
                    'Plan_id'           => $item->plan_id,
                    'Product_id'        => $item->product_id,

                    'Amount'              => $item->transaction_amount,
                    'Duration'            => $item->product_duration,
                    'Company Name'        => $item->company_name,
                    'Agent ID'            => $item->sales_agent,
                    'customersName'            => $item->beneficiary_name,
                    'Transaction ID'      => $item->cps_transaction_id,
                    'Reference ID'        => $item->referenceId,
                    'Next Charging Date'  => $item->recursive_charging_date,
                    'Subscription Date'   => $item->subscription_time,
                    'Free Look Period'    => $item->grace_period_time,
                ];
            }

            Log::channel('net_entrollment_api')->info('Today Net Enrollment API', [
                'date' => $today,
                'msisdn' => $request->msisdn,
                'total_records' => count($rows)
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Family Health Plan fetched successfully',
                'NetEnrollment' => $rows,
            ], 200);
        } catch (\Exception $e) {

            Log::channel('net_entrollment_api')->error('Net Enrollment API Error', [
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'msisdn' => $request->msisdn ?? null
            ]);

            return response()->json([
                'status' => 'Error',
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage() // production me remove kar dena
            ], 500);
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

    public function sendJazzSms(Request $request)
    {
        $msisdn = $request->msisdn;
        $message = $request->message;
        try {

            // ? Validation
            if (empty($msisdn)) {
                return [
                    'status' => false,
                    'message' => 'MSISDN is required'
                ];
            }

            if (empty($message)) {
                return [
                    'status' => false,
                    'message' => 'Message is required'
                ];
            }

            if (!is_string($message)) {
                return [
                    'status' => false,
                    'message' => 'Message must be a string'
                ];
            }

            // ? Clean MSISDN (only digits)
            $msisdn = preg_replace('/[^0-9]/', '', $msisdn);

            // ? Format MSISDN
            if (substr($msisdn, 0, 2) === '92') {
                // ok
            } elseif (substr($msisdn, 0, 1) === '0') {
                $msisdn = '92' . substr($msisdn, 1);
            } elseif (strlen($msisdn) === 10) {
                $msisdn = '92' . $msisdn;
            }

            // ? Final length check (Pakistan format)
            if (strlen($msisdn) != 12) {
                return [
                    'status' => false,
                    'message' => 'Invalid MSISDN format'
                ];
            }

            $key = 'mYjC!nc3dibleY3k';
            $iv  = 'Myin!tv3ctorjCM@';
            $cipher = 'AES-128-CBC';

            // ? Payload
            $payload = [
                'msisdn' => $msisdn,
                'content' => $message,
                'referenceId' => uniqid(),
            ];

            $jsonData = json_encode($payload);

            // ? Encryption check
            $encryptedBinary = openssl_encrypt($jsonData, $cipher, $key, OPENSSL_RAW_DATA, $iv);

            if ($encryptedBinary === false) {
                return [
                    'status' => false,
                    'message' => 'Encryption failed'
                ];
            }

            $encryptedHex = bin2hex($encryptedBinary);

            $requestBody = json_encode(['data' => $encryptedHex]);

            // ? API Call
            $ch = curl_init('https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification');

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_TIMEOUT => 30, // ? timeout add
                CURLOPT_POSTFIELDS => $requestBody,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
                    'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
                    'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
                ],
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            // ? cURL Error
            if ($response === false) {
                Log::channel('cashless_api')->error('Jazz SMS cURL Error', [
                    'error' => $error,
                    'payload' => $payload,
                ]);

                return [
                    'status' => false,
                    'message' => $error
                ];
            }

            // ? HTTP Error
            if ($httpCode != 200) {
                Log::channel('cashless_api')->error('Jazz SMS HTTP Error', [
                    'http_code' => $httpCode,
                    'response' => $response
                ]);

                return [
                    'status' => false,
                    'message' => 'HTTP Error: ' . $httpCode
                ];
            }

            $decoded = json_decode($response, true);

            // ? Invalid JSON response
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'status' => false,
                    'message' => 'Invalid JSON response'
                ];
            }

            Log::channel('cashless_api')->info('Jazz SMS Sent', [
                'payload' => $payload,
                'response' => $decoded,
            ]);

            return [
                'status' => true,
                'response' => $decoded
            ];
        } catch (\Throwable $e) {

            Log::channel('cashless_api')->error('Jazz SMS Exception', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }


    public function updateBeneficiaryName(Request $request)
    {
        try {

            $request->validate([
                'subscriber_msisdn' => 'required|string',
                'beneficiary_name'  => 'required|string',
            ]);

            // ? Step 1: Update beneficiary_name
            $updated = CustomerSubscription::where('subscriber_msisdn', $request->subscriber_msisdn)
                ->update([
                    'beneficiary_name' => $request->beneficiary_name
                ]);

            // ? Step 2: Call external API (customer_name update)
            $apiResponse = Http::get('https://jazzcash-health.efulife.com/api/updateCustomerName', [
                'customer_name'   => $request->beneficiary_name, // ?? mapping
                'customer_msisdn' => $request->subscriber_msisdn
            ]);

            if ($updated > 0) {
                return response()->json([
                    'status' => true,
                    'message' => 'Beneficiary updated & API called successfully',
                    'updated_records' => $updated,
                    'api_response' => $apiResponse->json() // optional
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'No record found'
            ], 404);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
