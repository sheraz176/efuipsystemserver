<?php

namespace App\Http\Controllers\Api;

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

class MobileApiController extends Controller
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


    public function getPlans(Request $request)
    {
        $activePlans = PlanModel::select('plan_id', 'plan_name', 'status')->where('status', 1)->get();
        return response()
            ->json([
                'status' => 'success',
                'data' => $activePlans,
            ])
            ->setStatusCode(200);
    }

    public function getProducts(Request $request)
{
    // Validate the request
    // $validator = Validator::make($request->all(), [
    //     'plan_id' => 'required|integer',
    // ]);

    // if ($validator->fails()) {
    //     return response()->json([
    //         'messageCode' => 400,
    //         'message' => 'Validation failed',
    //         'errors' => $validator->errors(),
    //     ], 400);
    // }

    // Retrieve active products associated with the specified plan ID
    $products = ProductModel::where('plan_id', '4')
    ->where('api_status', 1)
                            // ->where('status', 1)
                            ->get();

    // Filter out null and zero values
    $filteredProducts = $products->map(function ($product) {
        return collect($product)->filter(function ($value) {
            return !is_null($value) && $value !== 0;
        });
    });

    return response()->json([
        'status' => 'success',
        'data' => $filteredProducts,
    ], 200);
}





    public function jazz_app_subscription(Request $request)
    {

        $subscriber_cnic = $request->input("subscriber_cnic");
        $subscriber_msisdn = $request->input("subscriber_msisdn");
        $transaction_amount = $request->input("transaction_amount");
        $transactionStatus = $request->input("transactionStatus");
        $cpsOriginatorConversationId = $request->input("cpsOriginatorConversationId");
        $cpsTransactionId = $request->input("cpsTransactionId");
        $cpsResponse = $request->input("cpsResponse");
        $planId = $request->input("planId");
        $product_id = $request->input("product_id");
        $APIsource = $request->input("APIsource");

    // Perform validation
    $validator = Validator::make($request->all(), [
        'subscriber_msisdn' => 'required|numeric',
        'subscriber_cnic' => 'required|numeric',
        'transaction_amount' => 'required|numeric',
        'transactionStatus' => 'required|string',
        'cpsOriginatorConversationId' => 'required|string',
        'cpsTransactionId' => 'required|string',
        'cpsResponse' => 'required|string',
        'planId' => 'required|numeric',
         'product_id' => 'required|string',
        'APIsource' => 'required|string'
    ]);

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json(['error' => "true", 'messageCode' => 400, 'message' => $validator->errors()], 400);
    }

    $product = ProductModel::where('plan_id', $planId)
                        ->where('product_id', $product_id)
                        ->first();

                        // Check if product exists
	if (!$product) {
        return response()->json(['error' => "true", 'messageCode' => 404, 'message' => 'Product not found'], 404);
        }

        $transaction_amount = ProductModel::where('fee',$transaction_amount)
        ->where('product_id', $product_id)
        ->first();
        if (!$transaction_amount) {
            return response()->json(['error' => "true", 'messageCode' => 404, 'message' => 'Transaction Amount not Same Product Amount'], 404);
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

    if($subscription)
    {
	  $product_id= $subscription->productId;
    	  $product = ProductModel::where('product_id', $product_id)->first();
          $product_code_01=$product->product_code;

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
    }

    else{
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
            'Status Code' => 200
        ];

        // Return the response
        return response()->json($response);
    }

    }



    public function unsubscribePackage(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'id' => 'required',
        'subscriber_msisdn' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'messageCode' => 400,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    $subscriber_msisdn = $request->input('subscriber_msisdn');
    $subscriptionId = $request->input('id');

    // Get the subscription
    $subscription = CustomerSubscription::where('policy_status', 1)
       ->where('subscriber_msisdn',$subscriber_msisdn)
        ->where('subscription_id', $subscriptionId)
        ->first();

    if (!$subscription) {
        return response()->json(['error' => 'Subscription not found.'], 404);
    }

    // Handle non-refundable amounts

    $nonRefundableAmounts = ['4', '133','199', '163', '5', '10', '200', '2000', '1950', '1600', '5000','12','300','3000'];

    if (in_array($subscription->transaction_amount, $nonRefundableAmounts)) {
        $this->handleUnsubscription($subscription, $subscriber_msisdn);
        return response()->json([
            'status_code' => 200,
            'refund' => 'false',
            'message' => 'Package unsubscribed successfully. You are not eligible for a refund.',
        ]);
    }

    // Handle refundable amounts
    $today = Carbon::now('Asia/Karachi')->format('Y-m-d');
    $sub_date = Carbon::parse($subscription->created_at)->format('Y-m-d');
    $amount = $subscription->transaction_amount;

    if (in_array($amount, ['1'])) {
        if ($sub_date == $today)
                {
                    $refund_eligibility_time = Carbon::parse($subscription->created_at)->addHours(24)->format('Y-m-d H:i:s');
                    return response()->json([
                        'status_code' => 200,
                        'refund' => 'false',
                        'message' => 'You will be eligible for a refund after ' . $refund_eligibility_time,
                    ]);
                }

         else {
            return $this->handleRefund($subscription, $subscriber_msisdn);
        }
    }

    return response()->json(['message' => 'Transaction amount is not applicable for this check.'], 200);
}

private function handleUnsubscription($subscription, $subscriber_msisdn)
{
    CustomerUnSubscription::create([
        'unsubscription_datetime' => now(),
        'medium' => 'Mobile Api Application',
        'subscription_id' => $subscription->subscription_id,
        'refunded_id' => '1',
    ]);
    $subscription->update(['policy_status' => 0]);
}

private function handleRefund($subscription, $subscriber_msisdn)
{
    $grace_period_time = $subscription->grace_period_time;
    $current_time = date('Y-m-d H:i:s');
    $grace_period_datetime = new \DateTime($grace_period_time);
    $current_datetime = new \DateTime($current_time);

    if ($grace_period_datetime < $current_datetime) {
        $subscription->update(['policy_status' => 0]);
        return response()->json([
            'status_code' => 200,
            'refund' => 'false',
            'message' => 'Package unsubscribed successfully. You are not eligible for a refund because the grace period is over.',
        ]);
    } else {
        $refundedCustomer = RefundedCustomer::create([
            'subscription_id' => $subscription->subscription_id,
            'unsubscription_id' => 2,
            'transaction_id' => -1,
            'reference_id' => -1,
            'cps_response' => -1,
            'result_description' => -1,
            'result_code' => 0,
            'refunded_by' => $subscriber_msisdn,
            'medium' => 'Mobile Application',
        ]);

        CustomerUnSubscription::create([
            'unsubscription_datetime' => now(),
            'medium' => 'Mobile Application',
            'subscription_id' => $subscription->subscription_id,
            'refunded_id' => $refundedCustomer->refund_id,
        ]);

        $subscription->update(['policy_status' => 0]);
        $currentDateTime = date('Y-m-d H:i:s');

        if ($refundedCustomer) {
            $refundRow = [
                'subscriber_msisdn' => $subscriber_msisdn,
                'refund_amount' => $subscription->transaction_amount,
                'plan_code' => ProductModel::where('product_id', $subscription->productId)->first()->product_code,
                'refund_status' => 0,
                'RefundDate' => [
                    'date' => $currentDateTime,
                    'timezone_type' => 3,
                    'timezone' => 'Asia/Karachi',
                ],
                'IsAmountTransfer' => 0,
                'subscription_id' => $subscription->subscription_id,
                'updated_at' => $currentDateTime,
                'created_at' => $currentDateTime,
                'refund_id' => $refundedCustomer->refund_id,
                'id' => $subscription->subscription_id,
            ];

            return response()->json([
                'message' => 'Package unsubscribed successfully, and you are eligible for a refund.',
                'status_code' => 200,
                'refund' => 'true',
                'data_for_refund' => [
                    'Refund API Data' => $refundRow,
                    'refund_api' => 'https://portal.mhealth.efulife.com/mgmt/api/v3/mobileApi/updaterefund',
                ],
            ]);
        } else {
            return response()->json(['error' => 'No records updated.'], 404);
        }
    }
}


public function updaterefund(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'refund_id' => 'required',
        'transaction_id' => 'required',
        'reference_id' => 'required',
        'cps_response' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'messageCode' => 400,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    $refund_customer = RefundedCustomer::where('refund_id', $request->refund_id)->first();

    if (!$refund_customer) {
        return response()->json(['error' => 'Refund not found.'], 404);
    }

    // Check if the transaction_id is not equal to -1
    if ($refund_customer->transaction_id != -1) {
        return response()->json([
            'messageCode' => 4001,
            'message' => 'Case already closed.'
        ], 400);
    }

    $refund_customer->update([
        'transaction_id' => $request->transaction_id,
        'reference_id' => $request->reference_id,
        'cps_response' => $request->cps_response,
    ]);

    $refundData = [
        'refund_status' => "Refund Completed Both Ends EFU & JazzCash",
        'refund_case_status' => "Closed",
        'refund_id' => $refund_customer->refund_id,
        'transaction_id' => $refund_customer->transaction_id,
        'reference_id' => $refund_customer->reference_id,
        'cps_response' => $refund_customer->cps_response,
        'result_description' => $refund_customer->result_description,
    ];

    return response()->json($refundData);
}



public function activesubscriptions(Request $request)
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
    ->where('plan_id', 4)
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
                'planId' => $subscription->productId,
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
            'error' => false,
            'is_policy_data' => 'true',
            'message' => 'Customer Didnt Subscribed to any Policy',
            'Active Subscriptions' => []
        ]);
    }
}




}
