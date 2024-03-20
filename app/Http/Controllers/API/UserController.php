<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Plans\ProductModel;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Refund\RefundedCustomer;
use App\Models\Unsubscription\CustomerUnSubscription;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;



class Usercontroller extends Controller
{
    function index(Request $request)
    {
        $user= User::where('name', $request->name)->first();
        // print_r($data);
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'message' => ['These credentials do not match our records.']
                ], 404);
            }

            date_default_timezone_set('Asia/Karachi'); 
            $currentDateTime = date('Y-m-d H:i:s');
            $ldate = date('Y-m-d H:i:s', strtotime($currentDateTime . ' +30 minutes'));

             $token = $user->createToken('my-app-token')->plainTextToken;
        
            $response = [
                'token' => $token,
                'token expiration'=> $ldate
            ];
        
             return response($response, 201);
    }

public function getProducts(Request $request)
    {

        $planId = $request->input('plan_id');

    // Retrieve active products associated with the specified plan ID
    $products = ProductModel::where('plan_id', 1)
                            ->where('status', 1)
                            ->get();
    
    $transformedProducts = [];
    foreach ($products as $product) {
        $transformedProducts[] = [
            'id' => $product->product_id,
            'plan_name' => $product->product_name,
            'natural_death_benefit' => $product->natural_death_benefit,
            'accidental_death_benefit' => $product->accidental_death_benefit,
            'accidental_medicial_reimbursement' => $product->accidental_medicial_reimbursement,
            'annual_contribution' => $product->contribution,
            'plan_code' => $product->product_code,
            'fee' => $product->fee,
            'autoRenewal' => $product->autoRenewal,
            'duration' => $product->duration,
            'status' => $product->status,
            'scope_of_cover' => $product->scope_of_cover,
            'eligibility' => $product->eligibility,
            'other_key_details' => $product->other_key_details,
            'exclusions' => $product->exclusions,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];
    }

    return response()->json($transformedProducts); 
  
    }

public function Subscription(Request $request)
{
    // Retrieve data from the request
    $subscriber_cnic = $request->input("subscriber_cnic");
    $subscriber_msisdn = $request->input("subscriber_msisdn");
    $transaction_amount = $request->input("transaction_amount");
    $transactionStatus = $request->input("transactionStatus");
    $cpsOriginatorConversationId = $request->input("cpsOriginatorConversationId");
    $cpsTransactionId = $request->input("cpsTransactionId");
    $cpsResponse = $request->input("cpsResponse");
    $planId = $request->input("planId");
    $planCode = $request->input("planCode");
    $APIsource = $request->input("APIsource");

    // Perform validation
    $validator = Validator::make($request->all(), [
        'subscriber_cnic' => 'required|numeric',
        'subscriber_msisdn' => 'required|numeric',
        'transaction_amount' => 'required|numeric',
        'transactionStatus' => 'required|string',
        'cpsOriginatorConversationId' => 'required|string',
        'cpsTransactionId' => 'required|string',
        'cpsResponse' => 'required|string',
        'planId' => 'required|numeric',
        'planCode' => 'required|string',
        'APIsource' => 'required|string'
    ]);

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json(['error' => "true", 'messageCode' => 400, 'message' => $validator->errors()], 400);
    }

    // Retrieve product details
	$product = ProductModel::where('product_code', $planCode)
                        ->where('status', 1)
                        ->first();

// Check if product exists
	if (!$product) {
   		 return response()->json(['error' => "true", 'messageCode' => 404, 'message' => 'Product not found'], 404);
			}

	    




    // Calculate grace period and recursive charging date
    $grace_period = 14;
    $grace_period_time = date('Y-m-d H:i:s', strtotime("+$grace_period days"));
    $recursive_charging_date = date('Y-m-d H:i:s', strtotime("+" . $product->duration . " days"));

    // Create customer subscription record

    $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
            ->where('plan_id', 1)
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
            'transaction_amount' => $transaction_amount,
            'transaction_status' => $transactionStatus,
            'referenceId' => $cpsOriginatorConversationId,
            'cps_transaction_id' => $cpsTransactionId,
            'cps_response_text' => 'Service Activated Successfully',
            'product_duration' => $product->duration,
            'plan_id' => 1,
            'productId' => $product->product_id,
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
    ->where('plan_id', 1)
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

        public function unsubscribeactiveplan(Request $request)
    {
        $subscriber_msisdn = $request->input("subscriber_msisdn");
        $subscriptionId = $request->input("id");

        //Get Grace Period Time 
	$subscription = CustomerSubscription::where('policy_status', 1)
                                    ->where('subscription_id', $subscriptionId)
                                    ->first();     

        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found.'], 404);
        }

        $grace_period_time=$subscription->grace_period_time;
        $transaction_amount=$subscription->transaction_amount;
        $planCode=$subscription->planCode;
        $Subscription_id=$subscription->subscription_id;

	
	$product_id = $subscription->productId;
	
	
        // Retrieve the planCode using the product_id
        $product = ProductModel::where('product_id', $product_id)->first();
        $planCode = $product->product_code;


	


        $current_time=date('Y-m-d H:i:s');
        $grace_period_datetime = new \DateTime($grace_period_time);
        $current_datetime = new \DateTime($current_time);

        if ($grace_period_datetime < $current_datetime) {

            $current_datetime = new \DateTime($current_time);

            $subscription->update(['policy_status' => 0]);

        if ($subscription) {
            return response()->json(['status_code' => 200, 'refund' => 'false','message' => 'Package Unsubscribe Sucessfullly and Your are Not Eligible for Refund Because Grace Period is Over']);
        } else {
            return response()->json(['error' => 'No records updated.'], 404);
        }

        } elseif ($grace_period_datetime > $current_datetime) 
        {

            $current_datetime = new \DateTime($current_time);

                        
            $refundedCustomer=RefundedCustomer::create([
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
            'medium' => "Mobile Application",
            'subscription_id' => $subscription->subscription_id,
            'refunded_id' => $refundedCustomer->refund_id,
        ]); 

	$subscription->update(['policy_status' => 0]);
	$currentDateTime = date('Y-m-d H:i:s');
        if ($refundedCustomer) {

	$refundRow = [
        'subscriber_msisdn' => $subscriber_msisdn,
        'refund_amount' => $transaction_amount,
        'plan_code' => $planCode,
        'refund_status' => 0,
        'RefundDate' => [
            'date' => $currentDateTime,
            'timezone_type' => 3,
            'timezone' => 'Asia/Karachi'
        ],
        'IsAmountTransfer' => 0,
        'subscription_id' => $subscription->subscription_id,
        'updated_at' => $currentDateTime,
        'created_at' => $currentDateTime,
        'id' => $subscription->subscription_id
    	];

            return response()->json([
                'message' => 'Package Unsubscribe Sucessfullly, and You are Eligible for Refund',
                'status_code' => 200,
                'refund' => 'true',
                'data_for_refund' => [
                    'Refund API Data'=>$refundRow,
                    'refund_api' => 'https://jazzcash-ips.efulife.com/mgmt/public/api/v1/closeRefundCase',
                ]
            ]);

        } else {
            return response()->json(['error' => 'No records updated.'], 404);
        }


        } 
        
        else {
            return response()->json("Grace period time is the same as the current time.");
        }


    }
	
public function Update_refund_status(Request $request)
{
    $subscriber_msisdn = $request->input("subscriber_msisdn");
    $subscription_id = $request->input("subscription_id");

    $rules = [
        'subscriber_msisdn' => 'required|numeric',
        'subscription_id' => 'required|numeric'
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $subscription = CustomerSubscription::where('policy_status', 0)
                                        ->where('subscription_id', $subscription_id)
                                        ->first();

    if (!$subscription) {
        return response()->json([
            'Error' => true,
            'message' => 'Subscription ID is Not Matched from Record'
        ]);
    }

    // Check If Subscription_id is Valid
    else {
        $currentDateTime = date('Y-m-d H:i:s');
        $update_refund_1 = [
            [
                "id" => $subscription->subscription_id,
                "subscriber_msisdn" => $subscription->subscriber_msisdn,
                "refund_amount" => $subscription->transaction_amount,
                "plan_code" => '', // You need to provide the plan code here
                "refund_status" => 1,
                "RefundDate" => $currentDateTime,
                "IsAmountTransfer" => 1,
                "subscription_id" => $subscription->subscription_id,
                "created_at" => $currentDateTime,
                "updated_at" => $currentDateTime
            ]
        ];

        return response()->json([
            'Refund Status Updated' => 1,
            'Amount Refund Status' => 'Amount Transferred',
            'Refund Status' => 'Refund Case Closed',
            'Refund Data' => $update_refund_1
        ]);
    }
}
		
}
