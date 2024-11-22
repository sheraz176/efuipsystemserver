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

class GenericApiController extends Controller
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

    // Start Plan
    public function getPlans(Request $request)
    {

        // Check for required headers
        if (
            !$request->hasHeader('Authorization') ||
            !$request->hasHeader('X-User-Type') ||
            !$request->hasHeader('X-User-Role') ||
            !$request->hasHeader('X-App-Platform')
        ) {
            return response()->json([
                'error' => true,
                'message' => 'Required headers are missing',
                'messageCode' => 400
            ], 400);
        }

        // Get header values
        $userType = $request->header('X-User-Type');
        $userRole = $request->header('X-User-Role');
        $appPlatform = $request->header('X-App-Platform');


        if ($userType === 'USSD' && $userRole === 'Customer' && $appPlatform === 'CustomerUSSD') {
            return $this->customerUSSDPlan($request);
        } elseif ($userType === 'USSD' && $userRole === 'Merchant' && $appPlatform === 'MerchantUSSD') {
            return $this->customerUSSDPlan($request);
        } elseif ($userType === 'Mobile' && $userRole === 'Customer' && $appPlatform === 'CustomerMobileApp') {
            return $this->customerMobileAppPlan($request);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Invalid header values',
                'messageCode' => 401
            ], 401);
        }
    }

    private function customerUSSDPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'msisdn' => [
                'required',
                'regex:/^03\d{9}$/', // Only accept numbers starting with 0300 and followed by 7 digits
            ],
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 4002 ,
                'message' => 'Invalid mobile number. Please enter a valid number in the format 0300XXXXXXX.'
            ], 400 );
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
                'statusCode' => '2002',
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
                'statusCode' => '3000',
                'SubscribedPlans' => $subscribedPlans,
                'AvailablePlans' => $availablePlans,
            ]);
        } else {
            // All plans are available
            return response()->json([
                'statusCode' => '3000',
                'SubscribedPlans' => $subscribedPlans,
                'AvailablePlans' => $availablePlans,
            ]);
        }
    }
    private function customerMobileAppPlan(Request $request)
    {
        $activePlans = PlanModel::select('plan_id', 'plan_name', 'status')->where('status', 1)->get();
        return response()
            ->json([
                'status' => 'success',
                'statusCode' => '3000',
                'data' => $activePlans,
            ]);
    }
    // End Plan

    // Start Products
    public function getProducts(Request $request)
    {
        // Check for required headers
        if (
            !$request->hasHeader('Authorization') ||
            !$request->hasHeader('X-User-Type') ||
            !$request->hasHeader('X-User-Role') ||
            !$request->hasHeader('X-App-Platform')
        ) {
            return response()->json([
                'error' => true,
                'message' => 'Required headers are missing',
                'messageCode' => 400
            ], 400);
        }

        // Get header values
        $userType = $request->header('X-User-Type');
        $userRole = $request->header('X-User-Role');
        $appPlatform = $request->header('X-App-Platform');


        if ($userType === 'USSD' && $userRole === 'Customer' && $appPlatform === 'CustomerUSSD') {
            return $this->customerUSSDProduct($request);
        } elseif ($userType === 'USSD' && $userRole === 'Merchant' && $appPlatform === 'MerchantUSSD') {
            return $this->customerUSSDProduct($request);
        } elseif ($userType === 'Mobile' && $userRole === 'Customer' && $appPlatform === 'CustomerMobileApp') {
            return $this->customerMobileAppProduct($request);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Invalid header values',
                'messageCode' => 401
            ], 401);
        }
    }

    private function customerUSSDProduct(Request $request)
    {
        // Perform validation
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|numeric',
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 4002,
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
                'statusCode' => 3101,
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
            'statusCode' => 3100,
            'products' => $transformedProducts
        ]);
    }
    private function customerMobileAppProduct(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'messageCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Retrieve active products associated with the specified plan ID
        $products = ProductModel::where('plan_id', $request->plan_id)
            ->where('status', 1)
            ->get();

        // Filter out null, zero, and empty string values
        $filteredProducts = $products->map(function ($product) {
            return collect($product)->filter(function ($value) {
                return !is_null($value) && $value !== 0 && $value !== '';
            });
        });

        return response()->json([
            'status' => 'success',
            'statusCode' => 3100,
            'data' => $filteredProducts,
        ], 200);
    }
    // End Products

    // Start Subscription
    public function jazz_app_subscription(Request $request)
    {
        // Check for required headers
        if (
            !$request->hasHeader('Authorization') ||
            !$request->hasHeader('X-User-Type') ||
            !$request->hasHeader('X-User-Role') ||
            !$request->hasHeader('X-App-Platform')
        ) {
            return response()->json([
                'error' => true,
                'message' => 'Required headers are missing',
                'messageCode' => 400
            ], 400);
        }

        // Get header values
        $userType = $request->header('X-User-Type');
        $userRole = $request->header('X-User-Role');
        $appPlatform = $request->header('X-App-Platform');


        if ($userType === 'USSD' && $userRole === 'Customer' && $appPlatform === 'CustomerUSSD') {
            return $this->customerUSSDSubscription($request);
        } elseif ($userType === 'USSD' && $userRole === 'Merchant' && $appPlatform === 'MerchantUSSD') {
            return $this->merchantUSSDSubscription($request);
        } elseif ($userType === 'Mobile' && $userRole === 'Customer' && $appPlatform === 'CustomerMobileApp') {
            return $this->customerMobileAppSubscription($request);
        } elseif ($userType === 'Mobile' && $userRole === 'Merchant' && $appPlatform === 'MerchantMobileApp') {
            return $this->merchantMobileAppSubscription($request);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Invalid header values',
                'messageCode' => 401
            ], 401);
        }
    }

    private function customerUSSDSubscription(Request $request)
    {
        // Logic specific to Customer USSD
        // $subscriber_cnic = $request->input("subscriber_cnic");



        // Perform validation
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
            'product_id' => 'required|integer',
            'customer_msisdn' => 'required|string',
            'transaction_amount' => 'required|numeric',
            'cpsOriginatorConversationId' => 'required|string',
            'cpsTransactionId' => 'required|string',
            'cpsResponse' => 'required|string',

        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['statusCode' => 400, 'message' => $validator->errors()], 400);
        }

        $subscriber_msisdn = $request->input("customer_msisdn");
        $transaction_amount = $request->input("transaction_amount");
        $cpsOriginatorConversationId = $request->input("cpsOriginatorConversationId");
        $cpsTransactionId = $request->input("cpsTransactionId");
        $planId = $request->input("plan_id");
        $product_id = $request->input("product_id");
        $cpsResponse = $request->input("cpsResponse");

        $subscriber_cnic = "000000000000";
        $transactionStatus = "1";
        $product = ProductModel::where('plan_id', $planId)
            ->where('product_id', $product_id)
            ->first();
        // Check if product exists
        if (!$product) {
            return response()->json(['statusCode' => 3101, 'message' => 'Product not found'], 404);
        }
        $transaction_amount = ProductModel::where('fee', $transaction_amount)
            ->where('product_id', $product_id)
            ->first();
        if (!$transaction_amount) {
            return response()->json(['statusCode' => 4001, 'message' => 'Transaction Amount not Same Product Amount'], 404);
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
                'statusCode' => 4000,
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
                'cps_response_text' => $cpsResponse,
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
                'statusCode' => 2000,
                'message' => 'Customer Subscribed Sucessfully',
                'policy_subscription_id' => $subscription_data->subscription_id,
                'Information' => [
                    'subscriber_msisdn' => $subscription_data->subscriber_msisdn,
                    'transaction_amount' => $subscription_data->transaction_amount,
                    'transactionStatus' => $subscription_data->transaction_status,
                    'cpsResponse' => $subscription_data->cps_response_text,
                    'planId' => $subscription_data->plan_id,
                    'productId' => $subscription_data->productId,
                    'planCode' => $planCode,
                    'plan_status' => $subscription_data->policy_status,
                    'ApiSource' => $subscription_data->api_source,

                ],
                'statusCode' => 2000
            ];

            // Return the response
            return response()->json($response);
        }
    }
    private function merchantUSSDSubscription(Request $request)
    {
        // Logic specific to Merchant USSD

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
            'product_id' => 'required|integer',
            'customer_msisdn' => 'required|string',
            'marchant_msisdn' => 'required|string',
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

        $subscriber_msisdn = $request->input("customer_msisdn");
        $transaction_amount = $request->input("transaction_amount");
        $cpsOriginatorConversationId = $request->input("cpsOriginatorConversationId");
        $cpsTransactionId = $request->input("cpsTransactionId");
        $planId = $request->input("plan_id");
        $product_id = $request->input("product_id");
        $cpsResponse = $request->input("cpsResponse");
        $marchant_msisdn = $request->input("marchant_msisdn");

        $product = ProductModel::where('plan_id', $planId)
            ->where('product_id', $product_id)
            ->first();

        // Check if product exists
        if (!$product) {
            return response()->json(['statusCode' => 3101, 'message' => 'Product not found'], 404);
        }

        $transaction_amount = ProductModel::where('fee', $transaction_amount)
            ->where('product_id', $product_id)
            ->first();
        if (!$transaction_amount) {
            return response()->json(['statusCode' => 4001, 'message' => 'Transaction Amount not Same Product Amount'], 404);
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
                'statusCode' => 4000,
                'message' => 'Already subscribed to the plan.',
                'Policy Number' => $subscription['subscription_id'],
                'planCode' => $product_code_01,
                'transactionAmount' => $subscription['transaction_amount'],
                'Subscriber Number' =>  $subscription['subscriber_msisdn'],
                'Subcription Time'  =>  $subscription['subscription_time']
            ]);
        } else {

            $MarchantSubscriptionData = MarchantModel::create([
                'marchant_msisdn' => $marchant_msisdn,
                'customer_msisdn' => $subscriber_msisdn,
                'amount' => $amount,
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
                'cps_response_text' => $cpsResponse,
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
                'statusCode' => 2000,
                'message' => 'Customer Subscribed Sucessfully',
                'policy_subscription_id' => $subscription_data->subscription_id,
                'Information' => [
                    'subscriber_msisdn' => $subscription_data->subscriber_msisdn,
                    'transaction_amount' => $subscription_data->transaction_amount,
                    'transactionStatus' => $subscription_data->transaction_status,
                    'cpsResponse' => $subscription_data->cps_response_text,
                    'planId' => $subscription_data->plan_id,
                    'productId' => $subscription_data->productId,
                    'planCode' => $planCode,
                    'plan_status' => $subscription_data->policy_status,
                    'ApiSource' => $subscription_data->api_source,
                    'MerchantMsisdn' => $marchant_msisdn,

                ],
                'statusCode' => 2000
            ];

            // Return the response
            return response()->json($response);
        }
    }
    private function customerMobileAppSubscription(Request $request)
    {
        // Logic specific to Customer Mobile App



        // Perform validation
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
            'product_id' => 'required|integer',
            'customer_msisdn' => 'required|string',
            'transaction_amount' => 'required|numeric',
            'cpsOriginatorConversationId' => 'required|string',
            'cpsTransactionId' => 'required|string',
            'cpsResponse' => 'required|string',
            'transactionStatus' => 'required|string',
            'subscriber_cnic' => 'required|numeric',



        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['error' => "true", 'statusCode' => 400, 'message' => $validator->errors()], 400);
        }

        $subscriber_cnic = $request->input("subscriber_cnic");
        $subscriber_msisdn = $request->input("customer_msisdn");
        $transaction_amount = $request->input("transaction_amount");
        $transactionStatus = $request->input("transactionStatus");
        $cpsOriginatorConversationId = $request->input("cpsOriginatorConversationId");
        $cpsTransactionId = $request->input("cpsTransactionId");
        $cpsResponse = $request->input("cpsResponse");
        $planId = $request->input("plan_id");
        $product_id = $request->input("product_id");
        // $APIsource = $request->input("APIsource");

        $product = ProductModel::where('plan_id', $planId)
            ->where('product_id', $product_id)
            ->first();

        // Check if product exists
        if (!$product) {
            return response()->json(['error' => "true", 'statusCode' => 3101, 'message' => 'Product not found'], 404);
        }

        $transaction_amount = ProductModel::where('fee', $transaction_amount)
            ->where('product_id', $product_id)
            ->first();
        if (!$transaction_amount) {
            return response()->json(['error' => "true", 'statusCode' => 4001, 'message' => 'Transaction Amount not Same Product Amount'], 404);
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
                'statusCode' => 4000,
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
                'cps_response_text' => $cpsResponse,
                'product_duration' => $product->duration,
                'plan_id' => $planId,
                'productId' => $product_id,
                'policy_status' => 1,
                'pulse' => 'Recursive Charging',
                'api_source' => 'Jazz Application',
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
                'statusCode' => 2000,
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
                    'productId' => $subscription_data->productId,
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
                'statusCode' => 2000
            ];

            // Return the response
            return response()->json($response);
        }
    }
    private function merchantMobileAppSubscription(Request $request)
    {
        // Logic specific to Merchant Mobile App
        return response()->json([
            'message' => 'Merchant Mobile App Subscription processed Panding',
        ]);
    }
    // End Subscription

    // Start UnSubscription
    public function unsubscribePackage(Request $request)
    {

        // Check for required headers
        if (
            !$request->hasHeader('Authorization') ||
            !$request->hasHeader('X-User-Type') ||
            !$request->hasHeader('X-User-Role') ||
            !$request->hasHeader('X-App-Platform')
        ) {
            return response()->json([
                'error' => true,
                'message' => 'Required headers are missing',
                'messageCode' => 400
            ], 400);
        }

        // Get header values
        $userType = $request->header('X-User-Type');
        $userRole = $request->header('X-User-Role');
        $appPlatform = $request->header('X-App-Platform');


        if ($userType === 'USSD' && $userRole === 'Customer' && $appPlatform === 'CustomerUSSD') {
            return $this->customerUSSDUnSub($request);
        } elseif ($userType === 'USSD' && $userRole === 'Merchant' && $appPlatform === 'MerchantUSSD') {
            return $this->customerMarchantUnSub($request);
        } elseif ($userType === 'Mobile' && $userRole === 'Customer' && $appPlatform === 'CustomerMobileApp') {
            return $this->customerMobileAppUnSub($request);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Invalid header values',
                'messageCode' => 401
            ], 401);
        }
    }

    private function customerUSSDUnSub(Request $request)
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
                'statusCode' => 4004,
                'message' => 'No active subscriptions found for this subscriber.',
            ], 404);
        }
        // Step 3: If active subscriptions are found, return them to the user
        if (!$request->has('subscription_id')) {
            return response()->json([
                'statusCode' => 4000,
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
                'statusCode' => 4004,
                'message' => 'Subscription with the given ID not found in active subscriptions.',
            ], 404);
        }
        $nonRefundableAmounts = ['4', '133', '163', '5', '10', '200', '2000', '1950', '1600', '5000'];
        if (in_array($subscription->transaction_amount, $nonRefundableAmounts)) {
            // Handle non-refundable unsubscription
            CustomerUnSubscription::create([
                'unsubscription_datetime' => now(),
                'medium' => 'USSD',
                'subscription_id' => $subscription->subscription_id,
                'refunded_id' => '1',
            ]);
            $subscription->update(['policy_status' => 0]);

            return response()->json([
                'statusCode' => 2001,
                'refund' => 'false',
                'medium' => 'USSD',
                'message' => 'Package unsubscribed successfully. You are not eligible for a refund.',
            ]);
        }
    }
    private function customerMarchantUnSub(Request $request)
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
                'statusCode' => 4004,
                'message' => 'No active subscriptions found for this subscriber.',
            ], 404);
        }
        // Step 3: If active subscriptions are found, return them to the user
        if (!$request->has('subscription_id')) {
            return response()->json([
                'statusCode' => 4000,
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
                'statusCode' => 4004,
                'message' => 'Subscription with the given ID not found in active subscriptions.',
            ], 404);
        }
        $nonRefundableAmounts = ['4', '133', '163', '5', '10', '200', '2000', '1950', '1600', '5000'];
        if (in_array($subscription->transaction_amount, $nonRefundableAmounts)) {
            // Handle non-refundable unsubscription
            CustomerUnSubscription::create([
                'unsubscription_datetime' => now(),
                'medium' => 'Marchant USSD',
                'subscription_id' => $subscription->subscription_id,
                'refunded_id' => '1',
            ]);
            $subscription->update(['policy_status' => 0]);

            return response()->json([
                'statusCode' => 2001,
                'refund' => 'false',
                'medium' => 'Marchant USSD',
                'message' => 'Package unsubscribed successfully. You are not eligible for a refund.',
            ]);
        }
    }
    private function customerMobileAppUnSub(Request $request)
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
                'statusCode' => 4004,
                'message' => 'No active subscriptions found for this subscriber.',
            ], 404);
        }
        // Step 3: If active subscriptions are found, return them to the user
        if (!$request->has('subscription_id')) {
            return response()->json([
                'statusCode' => 4000,
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
                'statusCode' => 4004,
                'message' => 'Subscription with the given ID not found in active subscriptions.',
            ], 404);
        }
        $nonRefundableAmounts = ['4', '133', '163', '5', '10', '200', '2000', '1950', '1600', '5000'];
        if (in_array($subscription->transaction_amount, $nonRefundableAmounts)) {
            // Handle non-refundable unsubscription
            CustomerUnSubscription::create([
                'unsubscription_datetime' => now(),
                'medium' => 'Jazz Application',
                'subscription_id' => $subscription->subscription_id,
                'refunded_id' => '1',
            ]);
            $subscription->update(['policy_status' => 0]);

            return response()->json([
                'statusCode' => 2001,
                'refund' => 'false',
                'medium' => 'Jazz Application',
                'message' => 'Package unsubscribed successfully. You are not eligible for a refund.',
            ]);
        }
    }
    // End UnSubscription

    // Start Active SubScription

    public function activesubscriptions(Request $request)
    {

        // Check for required headers
        if (
            !$request->hasHeader('Authorization') ||
            !$request->hasHeader('X-User-Type') ||
            !$request->hasHeader('X-User-Role') ||
            !$request->hasHeader('X-App-Platform')
        ) {
            return response()->json([
                'error' => true,
                'message' => 'Required headers are missing',
                'messageCode' => 400
            ], 400);
        }

        // Get header values
        $userType = $request->header('X-User-Type');
        $userRole = $request->header('X-User-Role');
        $appPlatform = $request->header('X-App-Platform');


        if ($userType === 'USSD' && $userRole === 'Customer' && $appPlatform === 'CustomerUSSD') {
            return $this->ActiveSubGetAll($request);
        } elseif ($userType === 'USSD' && $userRole === 'Merchant' && $appPlatform === 'MerchantUSSD') {
            return $this->ActiveSubGetAll($request);
        } elseif ($userType === 'Mobile' && $userRole === 'Customer' && $appPlatform === 'CustomerMobileApp') {
            return $this->ActiveSubGetAll($request);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Invalid header values',
                'messageCode' => 401
            ], 401);
        }
    }

    private function ActiveSubGetAll(Request $request)
    {

        $subscriber_msisdn = $request->input("subscriber_msisdn");
        $rules = [
            'subscriber_msisdn' => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Retrieve the subscription details
        $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('policy_status', 1)
            ->first();

        if ($subscription) {
            // Retrieve the product_id from the subscription
            $product_id = $subscription->productId;


            // Retrieve the planCode using the product_id
            $product = ProductModel::where('product_id', $product_id)->first();
            $planCode = $product->product_code;

            // Modified here: Changing keys to match the older response and including product_id
            return response()->json([
                'error' => false,
                'is_policy_data' => 'true',
                'statusCode' => 4000,
                'message' => 'Active Policies',
                'Active Subscriptions' => [
                    [
                        'id' => $subscription->subscription_id,
                        'customer_id' => $subscription->customer_id,
                        'payer_cnic' => $subscription->payer_cnic,
                        'payer_msisdn' => $subscription->payer_msisdn,
                        'subscriber_cnic' => $subscription->subscriber_cnic,
                        'subscriber_msisdn' => $subscription->subscriber_msisdn,
                        'beneficinary_name' => $subscription->beneficinary_name,
                        'benficinary_msisdn' => $subscription->benficinary_msisdn,
                        'transaction_amount' => $subscription->transaction_amount,
                        'transactionStatus' => $subscription->transaction_status,
                        'cpsOriginatorConversationId' => $subscription->referenceId,
                        'cpsTransactionId' => $subscription->cps_transaction_id,
                        'cpsRefundTransactionId' => -1,
                        'cpsResponse' => $subscription->cps_response_text,
                        'planId' => $subscription->plan_id,
                        'planCode' => $planCode, // Use the retrieved planCode here
                        'plan_status' => 1,
                        'pulse' => $subscription->pulse,
                        'APIsource' => $subscription->api_source,
                        'Recusive_charing_date' => $subscription->recursive_charging_date,
                        'subcription_time' => $subscription->subscription_time,
                        'grace_period_time' => $subscription->grace_period_time,
                        'Sales_agent' => $subscription->sales_agent,
                        'created_at' => $subscription->created_at,
                        'updated_at' => $subscription->updated_at,
                        'product_id' => $product_id  // Include product_id in the response
                    ]
                ]
            ]);
        } else {
            // Modified here: Returning null instead of an empty array
            return response()->json([
                'error' => true,
                'is_policy_data' => 'false',
                'statusCode' => 4004,
                'message' => 'Customer Didnt Subscribed to any Policy',
                'Active Subscriptions' => []
            ]);
        }
    }

    // End Active SubScription

}
