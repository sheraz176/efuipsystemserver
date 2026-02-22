<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\User;
use App\Models\Refund\RefundedCustomer;
use Illuminate\Support\Facades\Hash;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\MarchantModel;

class USSDAPI23Controller extends Controller
{
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'messageCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Attempt to retrieve the user
        $user = User::where('name', $request->name)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => ['These credentials do not match our records.'],
            ], 404);
        }

        // Create token and set expiration time
        $token = $user->createToken('my-app-token')->plainTextToken;
        $tokenExpiration = Carbon::now('Asia/Karachi')->addMinutes(30)->format('Y-m-d H:i:s');

        // Prepare response
        $response = [
            'token' => $token,
            'token_expiration' => $tokenExpiration,
        ];

        return response()->json($response, 201);
    }

    public function fatchPlans(Request $request)
{
    // Perform validation
    // Perform validation
    $validator = Validator::make($request->all(), [
        'msisdn' => [
            'required',
            'regex:/^0300\d{7}$/', // Only accept numbers starting with 0300 and followed by 7 digits
        ],
    ]);

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json([
            'statusCode' => 400,
            'message' => 'Invalid mobile number. Please enter a valid number in the format 0300XXXXXXX.'
        ], 400);
    }

    // Define the target plan IDs to check
    $targetPlanIds = [1, 4, 5];

    // Retrieve the number of active subscriptions for the specified plans
    $subscriptionCount = CustomerSubscription::where('subscriber_msisdn', $request->msisdn)
        ->whereIn('plan_id', $targetPlanIds)
        ->where('policy_status', 1)
        ->count();

    // Condition 1: All three plans are subscribed
    if ($subscriptionCount == 3) {
        return response()->json([
            'statusCode' => '2001',
            'message' => 'All Plans Subscribed',
        ]);
    }

    // Condition 2: Retrieve the user's subscribed plans
    $subscriptions = CustomerSubscription::where('subscriber_msisdn', $request->msisdn)
        ->where('policy_status', 1)
        ->whereIn('plan_id', $targetPlanIds)
        ->get();

    $subscribedPlans = [];
    $subscribedPlanIds = []; // To track subscribed plan IDs

    foreach ($subscriptions as $subscription) {
        $product_id = $subscription->productId;
        $plan_id = $subscription->plan_id;
        $product = ProductModel::where('product_id', $product_id)->first();
        $planCode = $product->product_code;

        // Add subscribed plan details to the array
        $subscribedPlans[] = [
            'planId' => $plan_id,
            'planName' => $planCode,
        ];

        // Store subscribed plan IDs to exclude them from available plans
        $subscribedPlanIds[] = $plan_id;
    }

    // Condition 3: Get available plans that are not in the subscribed list and have status = 1
    $availablePlans = PlanModel::select('plan_id', 'plan_name')
        ->whereIn('plan_id', $targetPlanIds)
        ->whereNotIn('plan_id', $subscribedPlanIds)
        ->where('status', 1)
        ->get();

    // Determine the response based on the number of subscriptions
    if ($subscriptionCount == 2 && $availablePlans->isNotEmpty()) {
        // Two plans are subscribed, one is available
        return response()->json([
            'statusCode' => '200',
            'SubscribedPlans' => $subscribedPlans,
            'AvailablePlans' => $availablePlans,
        ]);
    } else {
        // All plans are available
        return response()->json([
            'statusCode' => '200',
            'SubscribedPlans' => $subscribedPlans,
            'AvailablePlans' => $availablePlans,
        ]);
    }
}


public function fatchProducts(Request $request)
{
    // Perform validation
    $validator = Validator::make($request->all(), [
        'plan_id' => 'required|numeric',
    ]);

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json([
            'statusCode' => 400,
            'message' => 'Invalid Plan ID',
        ], 400);
    }

    $planId = $request->input('plan_id');

    // Retrieve active products associated with the specified plan ID
    $products = ProductModel::where('plan_id', $planId)
        ->where('status', 1)
        ->get();

    // Check if any products are available
    if ($products->isEmpty()) {
        return response()->json([
            'statusCode' => 400,
            'message' => 'No Products Available for the Specified Plan ID',
        ], 400);
    }

    // Transform the product data
    $transformedProducts = $products->map(function ($product) {
        return [
            'Product_Id' => $product->product_id,
            'PlanName' => $product->product_name,
            'Fee' => $product->fee,
            'PlanCode' => $product->product_code,
        ];
    });

    return response()->json([
        'statusCode' => 200,
        'products' => $transformedProducts
    ]);
}


    public function jazz_app_subscription_new(Request $request)
    {
        //  dd($request->all());
        // $subscriber_cnic = $request->input("subscriber_cnic");
        $subscriber_msisdn = $request->input("subscriber_msisdn");
        $transaction_amount = $request->input("transaction_amount");
        $cpsOriginatorConversationId = $request->input("cpsOriginatorConversationId");
        $cpsTransactionId = $request->input("cpsTransactionId");
        $planId = $request->input("planId");
        $product_id = $request->input("product_id");
        $cpsResponse = $request->input("cpsResponse");


        // Perform validation
        $validator = Validator::make($request->all(), [
            'subscriber_msisdn' => 'required|numeric',
            'planId' => 'required|numeric',
            'product_id' => 'required|string',
            'transaction_amount' => 'required|numeric',
            'cpsOriginatorConversationId' => 'required|string',
            'cpsTransactionId' => 'required|string',
            'cpsResponse' => 'required|string',


        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['statusCode' => 400, 'message' => $validator->errors()], 400);
        }

        $subscriber_cnic = "000000000000";
        $transactionStatus = "1";

        // dd($source);
        // if ($APIsource === '001') {
        //     $api_source = "USSD Subscription";
        // } elseif ($APIsource === '002') {
        //     $api_source = "Jazz Application";
        // } else {
        //     // Handle incorrect API source
        //     return response()->json(['error' => "true", 'messageCode' => 404, 'message' => 'APISource wrong'], 404);
        // }


        $product = ProductModel::where('plan_id', $planId)
            ->where('product_id', $product_id)
            ->first();

        // Check if product exists
        if (!$product) {
            return response()->json(['statusCode' => 404, 'message' => 'Product not found'], 404);
        }





        $transaction_amount = ProductModel::where('fee', $transaction_amount)
            ->where('product_id', $product_id)
            ->first();
        if (!$transaction_amount) {
            return response()->json(['statusCode' => 404, 'message' => 'Transaction Amount not Same Product Amount'], 404);
        }
        $amount = $transaction_amount->fee;
        //return "getting response of product:".$product;

        $grace_period = 14;
        $grace_period_time = date('Y-m-d H:i:s', strtotime("+$grace_period days"));
        $recursive_charging_date = date('Y-m-d H:i:s', strtotime("+" . $product->duration . " days"));



        $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('plan_id', $planId)
            ->where('policy_status', 1)
            ->first();

        if ($subscription) {
            $product_id = $subscription->productId;
            $product = ProductModel::where('product_id', $product_id)->first();
            $product_code_01 = $product->product_code;

            return response()->json([
                'error' => false,
                'messageCode' => 2001,
                'message' => 'Already subscribed to the plan.',
                'Policy Number' => $subscription['subscription_id'],
                'planCode' => $product_code_01,
                'transactionAmount' => $subscription['transaction_amount'],
                'Subscriber Number' =>  $subscription['subscriber_msisdn'],
                'Subcription Time'  =>  $subscription['subscription_time']
            ]);
        } else {
            $customer_subscription = CustomerSubscription::create([
                'customer_id' => '0011' . $subscriber_msisdn,
                'payer_cnic' => 1,
                'payer_msisdn' => $subscriber_msisdn,
                'subscriber_cnic' => $subscriber_cnic,
                'subscriber_msisdn' => $subscriber_msisdn,
                'beneficiary_name' => 'Need to Filled in Future',
                'beneficiary_msisdn' => 0,
                'transaction_amount' => $amount,
                'transaction_status' => $transactionStatus,
                'referenceId' => $cpsOriginatorConversationId,
                'cps_transaction_id' => $cpsTransactionId,
                'cps_response_text' => 'Service Activated Successfully',
                'product_duration' => $product->duration,
                'plan_id' => $planId,
                'productId' => $product_id,
                'policy_status' => 1,
                'pulse' => "USSD Subscription",
                'api_source' => "USSD Subscription",
                'recursive_charging_date' => $recursive_charging_date,
                'subscription_time' => now(),
                'grace_period_time' => $grace_period_time,
                'sales_agent' => 1,
                'company_id' => 15
            ]);

            // Retrieve subscription data
            $subscription_data = CustomerSubscription::find($customer_subscription->subscription_id);



            $product_id = $subscription_data->productId;

            // Retrieve the product details based on the product_id

            $product = ProductModel::find($product_id);

            $planCode = $product->product_code;


            // Construct the response
            $response = [
                'error' => "false",
                'messageCode' => 2002,
                'message' => 'Customer Subscribed Sucessfully',
                'policy_subscription_id' => $subscription_data->subscription_id,
                'Information' => [
                    'customer_id' => $subscription_data->customer_id,
                    'payer_cnic' => $subscription_data->payer_cnic,
                    'payer_msisdn' => $subscription_data->payer_msisdn,
                    'subscriber_cnic' => $subscription_data->subscriber_cnic,
                    'subscriber_msisdn' => $subscription_data->subscriber_msisdn,
                    'beneficinary_name' => $subscription_data->beneficinary_name,
                    'benficinary_msisdn' => $subscription_data->benficinary_msisdn,
                    'transaction_amount' => $subscription_data->transaction_amount,
                    'transactionStatus' => $subscription_data->transaction_status,
                    'cpsOriginatorConversationId' => $subscription_data->referenceId,
                    'cpsTransactionId' => $subscription_data->cps_transaction_id,
                    'cpsResponse' => $subscription_data->cps_response_text,
                    'planId' => $subscription_data->plan_id,
                    'planCode' => $planCode,
                    'plan_status' => $subscription_data->policy_status,
                    'pulse' => $subscription_data->pulse,
                    'APIsource' => $subscription_data->api_source,
                    'Recusive_charing_date' => $subscription_data->recursive_charging_date,
                    'subcription_time' => $subscription_data->subscription_time,
                    'grace_period_time' => $subscription_data->grace_period_time,
                    'Sales_agent' => $subscription_data->sales_agent,
                    'id' => $subscription_data->subscription_id
                ],
                'StatusCode' => 200
            ];

            // Return the response
            return response()->json($response);
        }
    }


    public function jazz_app_subscription_app(Request $request)
    {
        //  dd($request->all());
        // $subscriber_cnic = $request->input("subscriber_cnic");
        $subscriber_msisdn = $request->input("subscriber_msisdn");
        $transaction_amount = $request->input("transaction_amount");
        $cpsOriginatorConversationId = $request->input("cpsOriginatorConversationId");
        $cpsTransactionId = $request->input("cpsTransactionId");
        $planId = $request->input("planId");
        $product_id = $request->input("product_id");
        $cpsResponse = $request->input("cpsResponse");


        // Perform validation
        $validator = Validator::make($request->all(), [
            'subscriber_msisdn' => 'required|numeric',
            'planId' => 'required|numeric',
            'product_id' => 'required|string',
            'transaction_amount' => 'required|numeric',
            'cpsOriginatorConversationId' => 'required|string',
            'cpsTransactionId' => 'required|string',
            'cpsResponse' => 'required|string',


        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['statusCode' => 400, 'message' => $validator->errors()], 400);
        }

        $subscriber_cnic = "000000000000";
        $transactionStatus = "1";

        // dd($source);
        // if ($APIsource === '001') {
        //     $api_source = "USSD Subscription";
        // } elseif ($APIsource === '002') {
        //     $api_source = "Jazz Application";
        // } else {
        //     // Handle incorrect API source
        //     return response()->json(['error' => "true", 'messageCode' => 404, 'message' => 'APISource wrong'], 404);
        // }


        $product = ProductModel::where('plan_id', $planId)
            ->where('product_id', $product_id)
            ->first();

        // Check if product exists
        if (!$product) {
            return response()->json(['statusCode' => 404, 'message' => 'Product not found'], 404);
        }





        $transaction_amount = ProductModel::where('fee', $transaction_amount)
            ->where('product_id', $product_id)
            ->first();
        if (!$transaction_amount) {
            return response()->json(['statusCode' => 404, 'message' => 'Transaction Amount not Same Product Amount'], 404);
        }
        $amount = $transaction_amount->fee;
        //return "getting response of product:".$product;

        $grace_period = 14;
        $grace_period_time = date('Y-m-d H:i:s', strtotime("+$grace_period days"));
        $recursive_charging_date = date('Y-m-d H:i:s', strtotime("+" . $product->duration . " days"));



        $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('plan_id', $planId)
            ->where('policy_status', 1)
            ->first();

        if ($subscription) {
            $product_id = $subscription->productId;
            $product = ProductModel::where('product_id', $product_id)->first();
            $product_code_01 = $product->product_code;

            return response()->json([
                'error' => false,
                'messageCode' => 2001,
                'message' => 'Already subscribed to the plan.',
                'Policy Number' => $subscription['subscription_id'],
                'planCode' => $product_code_01,
                'transactionAmount' => $subscription['transaction_amount'],
                'Subscriber Number' =>  $subscription['subscriber_msisdn'],
                'Subcription Time'  =>  $subscription['subscription_time']
            ]);
        } else {
            $customer_subscription = CustomerSubscription::create([
                'customer_id' => '0011' . $subscriber_msisdn,
                'payer_cnic' => 1,
                'payer_msisdn' => $subscriber_msisdn,
                'subscriber_cnic' => $subscriber_cnic,
                'subscriber_msisdn' => $subscriber_msisdn,
                'beneficiary_name' => 'Need to Filled in Future',
                'beneficiary_msisdn' => 0,
                'transaction_amount' => $amount,
                'transaction_status' => $transactionStatus,
                'referenceId' => $cpsOriginatorConversationId,
                'cps_transaction_id' => $cpsTransactionId,
                'cps_response_text' => 'Service Activated Successfully',
                'product_duration' => $product->duration,
                'plan_id' => $planId,
                'productId' => $product_id,
                'policy_status' => 1,
                'pulse' => "Jazz Application",
                'api_source' => "Jazz Application",
                'recursive_charging_date' => $recursive_charging_date,
                'subscription_time' => now(),
                'grace_period_time' => $grace_period_time,
                'sales_agent' => 1,
                'company_id' => 15
            ]);

            // Retrieve subscription data
            $subscription_data = CustomerSubscription::find($customer_subscription->subscription_id);



            $product_id = $subscription_data->productId;

            // Retrieve the product details based on the product_id

            $product = ProductModel::find($product_id);

            $planCode = $product->product_code;


            // Construct the response
            $response = [
                'error' => "false",
                'messageCode' => 2002,
                'message' => 'Customer Subscribed Sucessfully',
                'policy_subscription_id' => $subscription_data->subscription_id,
                'Information' => [
                    'customer_id' => $subscription_data->customer_id,
                    'payer_cnic' => $subscription_data->payer_cnic,
                    'payer_msisdn' => $subscription_data->payer_msisdn,
                    'subscriber_cnic' => $subscription_data->subscriber_cnic,
                    'subscriber_msisdn' => $subscription_data->subscriber_msisdn,
                    'beneficinary_name' => $subscription_data->beneficinary_name,
                    'benficinary_msisdn' => $subscription_data->benficinary_msisdn,
                    'transaction_amount' => $subscription_data->transaction_amount,
                    'transactionStatus' => $subscription_data->transaction_status,
                    'cpsOriginatorConversationId' => $subscription_data->referenceId,
                    'cpsTransactionId' => $subscription_data->cps_transaction_id,
                    'cpsResponse' => $subscription_data->cps_response_text,
                    'planId' => $subscription_data->plan_id,
                    'planCode' => $planCode,
                    'plan_status' => $subscription_data->policy_status,
                    'pulse' => $subscription_data->pulse,
                    'APIsource' => $subscription_data->api_source,
                    'Recusive_charing_date' => $subscription_data->recursive_charging_date,
                    'subcription_time' => $subscription_data->subscription_time,
                    'grace_period_time' => $subscription_data->grace_period_time,
                    'Sales_agent' => $subscription_data->sales_agent,
                    'id' => $subscription_data->subscription_id
                ],
                'StatusCode' => 200
            ];

            // Return the response
            return response()->json($response);
        }
    }


    public function unsubscribePackage(Request $request)
{
    // Step 1: Validate the request to ensure `subscriber_msisdn` is provided
    $validator = Validator::make($request->all(), [
        'subscriber_msisdn' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'statusCode' => 400,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    // Step 2: Check for active subscriptions based on `subscriber_msisdn`
    $subscriptions = CustomerSubscription::where('subscriber_msisdn', $request->subscriber_msisdn)
        ->where('policy_status', 1) // Only active subscriptions
        ->get();

    // If no active subscriptions are found, return a message
    if ($subscriptions->isEmpty()) {
        return response()->json([
            'statusCode' => 404,
            'message' => 'No active subscriptions found for this subscriber.',
        ], 404);
    }

    // Step 3: If active subscriptions are found, return them to the user
    if (!$request->has('subscription_id')) {
        return response()->json([
            'statusCode' => 200,
            'message' => 'Active subscriptions found.',
            'subscriptions' => $subscriptions->map(function ($subscription) {
                return [
                    'subscription_id' => $subscription->subscription_id,
                    'plan_id' => $subscription->plan_id,
                    'transaction_amount' => $subscription->transaction_amount,
                ];
            }),
        ]);
    }

    // Step 4: If `subscription_id` is provided, proceed to unsubscribe
    $subscriptionId = $request->input('subscription_id');
    $subscription = $subscriptions->where('subscription_id', $subscriptionId)->first();

    if (!$subscription) {
        return response()->json([
            'statusCode' => 404,
            'message' => 'Subscription with the given ID not found in active subscriptions.',
        ], 404);
    }

    // Step 5: Check if the transaction amount is non-refundable
    $nonRefundableAmounts = ['4', '133', '163', '5', '10', '200', '2000', '1950', '1600'];
    if (in_array($subscription->transaction_amount, $nonRefundableAmounts)) {
        // Handle non-refundable unsubscription
        $this->handleUnsubscription($subscription, $request->subscriber_msisdn);
        return response()->json([
            'status_code' => 200,
            'refund' => 'false',
            'message' => 'Package unsubscribed successfully. You are not eligible for a refund.',
        ]);
    }

    // Step 6: If refundable, proceed with unsubscription and mark as eligible for a refund
    $this->handleUnsubscription($subscription, $request->subscriber_msisdn);
    return response()->json([
        'status_code' => 200,
        'refund' => 'true',
        'message' => 'Package unsubscribed successfully. Refund eligibility confirmed.',
    ]);
}


    private function handleUnsubscription($subscription, $subscriber_msisdn)
    {
        CustomerUnSubscription::create([
            'unsubscription_datetime' => now(),
            'medium' => 'USSD',
            'subscription_id' => $subscription->subscription_id,
            'refunded_id' => '1',
        ]);
        $subscription->update(['policy_status' => 0]);
    }




    public function marchant_subscription(Request $request)
    {
        //  dd($request->all());
        // $subscriber_cnic = $request->input("subscriber_cnic");



        // Perform validation
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
            'product_id' => 'required|integer',
            'customer_msisdn' => 'required|string',
            'marchant_msisdn' => 'required|string',
            'transaction_amount' => 'required|numeric',
            'cpsOriginatorConversationId' => 'required|string',
            'cpsTransactionId' => 'required|string',
            'cpsResponse' => 'required|string',
            'pulse'  => 'required|string',

        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['statusCode' => 400, 'message' => $validator->errors()], 400);
        }

        $subscriber_cnic = "000000000000";
        $transactionStatus = "1";

        $subscriber_msisdn = $request->input("customer_msisdn");
        $transaction_amount = $request->input("transaction_amount");
        $cpsOriginatorConversationId = $request->input("cpsOriginatorConversationId");
        $cpsTransactionId = $request->input("cpsTransactionId");
        $planId = $request->input("plan_id");
        $product_id = $request->input("product_id");
        $cpsResponse = $request->input("cpsResponse");
        $marchant_msisdn = $request->input("marchant_msisdn");

        // dd($source);
        // if ($APIsource === '001') {
        //     $api_source = "USSD Subscription";
        // } elseif ($APIsource === '002') {
        //     $api_source = "Jazz Application";
        // } else {
        //     // Handle incorrect API source
        //     return response()->json(['error' => "true", 'messageCode' => 404, 'message' => 'APISource wrong'], 404);
        // }


        $product = ProductModel::where('plan_id', $planId)
            ->where('product_id', $product_id)
            ->first();

        // Check if product exists
        if (!$product) {
            return response()->json(['statusCode' => 404, 'message' => 'Product not found'], 404);
        }





        $transaction_amount = ProductModel::where('fee', $transaction_amount)
            ->where('product_id', $product_id)
            ->first();
        if (!$transaction_amount) {
            return response()->json(['statusCode' => 404, 'message' => 'Transaction Amount not Same Product Amount'], 404);
        }
        $amount = $transaction_amount->fee;
        //return "getting response of product:".$product;

        $grace_period = 14;
        $grace_period_time = date('Y-m-d H:i:s', strtotime("+$grace_period days"));
        $recursive_charging_date = date('Y-m-d H:i:s', strtotime("+" . $product->duration . " days"));



        $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('plan_id', $planId)
            ->where('policy_status', 1)
            ->first();

        if ($subscription) {
            $product_id = $subscription->productId;
            $product = ProductModel::where('product_id', $product_id)->first();
            $product_code_01 = $product->product_code;

            return response()->json([
                'error' => false,
                'messageCode' => 2001,
                'message' => 'Already subscribed to the plan.',
                'Policy Number' => $subscription['subscription_id'],
                'planCode' => $product_code_01,
                'transactionAmount' => $subscription['transaction_amount'],
                'Subscriber Number' =>  $subscription['subscriber_msisdn'],
                'Subcription Time'  =>  $subscription['subscription_time']
            ]);
        } else {

            $MarchantSubscriptionData = MarchantModel::create([
                'marchant_msisdn' =>$marchant_msisdn,
                'customer_msisdn' => $subscriber_msisdn,
                'amount' =>$amount,
                'status' => 'success'
            ]);

            $customer_subscription = CustomerSubscription::create([
                'marchant_id' => $MarchantSubscriptionData->id,
                'customer_id' => '0011' . $subscriber_msisdn,
                'payer_cnic' => 1,
                'payer_msisdn' => $subscriber_msisdn,
                'subscriber_cnic' => $subscriber_cnic,
                'subscriber_msisdn' => $subscriber_msisdn,
                'beneficiary_name' => 'Need to Filled in Future',
                'beneficiary_msisdn' => 0,
                'transaction_amount' => $amount,
                'transaction_status' => $transactionStatus,
                'referenceId' => $cpsOriginatorConversationId,
                'cps_transaction_id' => $cpsTransactionId,
                'cps_response_text' => 'Service Activated Successfully',
                'product_duration' => $product->duration,
                'plan_id' => $planId,
                'productId' => $product_id,
                'policy_status' => 1,
                'pulse' => "Marchant Api",
                'api_source' => "Marchant Api",
                'recursive_charging_date' => $recursive_charging_date,
                'subscription_time' => now(),
                'grace_period_time' => $grace_period_time,
                'sales_agent' => 1,
                'company_id' => 17
            ]);

            // Retrieve subscription data
            $subscription_data = CustomerSubscription::find($customer_subscription->subscription_id);



            $product_id = $subscription_data->productId;

            // Retrieve the product details based on the product_id

            $product = ProductModel::find($product_id);

            $planCode = $product->product_code;


            // Construct the response
            $response = [
                'error' => "false",
                'messageCode' => 2002,
                'message' => 'Customer Subscribed Sucessfully',
                'policy_subscription_id' => $subscription_data->subscription_id,
                'Information' => [
                    'customer_id' => $subscription_data->customer_id,
                    'payer_cnic' => $subscription_data->payer_cnic,
                    'payer_msisdn' => $subscription_data->payer_msisdn,
                    'subscriber_cnic' => $subscription_data->subscriber_cnic,
                    'subscriber_msisdn' => $subscription_data->subscriber_msisdn,
                    'beneficinary_name' => $subscription_data->beneficinary_name,
                    'benficinary_msisdn' => $subscription_data->benficinary_msisdn,
                    'transaction_amount' => $subscription_data->transaction_amount,
                    'transactionStatus' => $subscription_data->transaction_status,
                    'cpsOriginatorConversationId' => $subscription_data->referenceId,
                    'cpsTransactionId' => $subscription_data->cps_transaction_id,
                    'cpsResponse' => $subscription_data->cps_response_text,
                    'planId' => $subscription_data->plan_id,
                    'planCode' => $planCode,
                    'plan_status' => $subscription_data->policy_status,
                    'Recusive_charing_date' => $subscription_data->recursive_charging_date,
                    'subcription_time' => $subscription_data->subscription_time,
                    'grace_period_time' => $subscription_data->grace_period_time,
                    'Sales_agent' => $subscription_data->sales_agent,
                    'id' => $subscription_data->subscription_id,
                    'pulse' => $subscription_data->pulse,
                    'APIsource' => $subscription_data->api_source,
                    'MerchantNo' => $marchant_msisdn,

                ],
                'StatusCode' => 200
            ];

            // Return the response
            return response()->json($response);
        }
    }




}
