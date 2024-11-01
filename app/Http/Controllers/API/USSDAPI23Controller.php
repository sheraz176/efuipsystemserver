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
        // dd($request->all());
         // Perform validation
         $validator = Validator::make($request->all(), [
            'msisdn' => 'required|numeric',
           ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['error' => "true", 'messageCode' => 400, 'message' => $validator->errors()], 400);
        }

          // Retrieve the subscription details
          $subscriptionCount = CustomerSubscription::where('subscriber_msisdn', $request->msisdn)
          ->whereIn('plan_id', [1, 4]) // Check for plan_id 1 and 4
          ->where('policy_status', 1)   // Only active policies
          ->count();

            // Check if both plans are already subscribed
            if ($subscriptionCount == 2) {
                return response()->json([
                    'error' => false,
                    'message' => 'Already two plans subscribed',
                ]);
            }

             // Retrieve the subscription details
             $subscription = CustomerSubscription::where('subscriber_msisdn', $request->msisdn)
             ->where('policy_status', 1)
             ->first();

             if ($subscription) {
            $product_id = $subscription->productId;
            $plan_id = $subscription->plan_id;
            $product = ProductModel::where('product_id', $product_id)->first();
            $planCode = $product->product_code;
            $avalablyPlans = PlanModel::select('plan_id', 'plan_name', 'status')
            ->where('plan_id', '!=', $plan_id)->where('status', 1)->get();
                //  dd($avalablyPlans);

                return response()->json([
                    'error' => false,
                    'is_policy_data' => 'true',
                    'message' => 'Customer is already Subscribed to Policy',
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
                        ],

                        'Available plan' =>
                        [
                            'status' => 'success',
                            'data' => $avalablyPlans,
                        ]


                    ]
                ]);

             }
             else{
                $activePlans = PlanModel::select('plan_id', 'plan_name', 'status')->where('status', 1)->get();
                return response()
                    ->json([
                        'status' => 'success',
                        'data' => $activePlans,
                    ])
                    ->setStatusCode(200);
             }


    }


    public function fatchProducts(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|numeric',
           ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['error' => "true", 'messageCode' => 400, 'message' => $validator->errors()], 400);
        }

        $planId  = $request->input('plan_id');
        // Retrieve active products associated with the specified plan ID
        $products = ProductModel::where('plan_id', $planId)
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

    public function jazz_app_subscription_new(Request $request)
    {
        //  dd($request->all());
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

        // dd($source);
        if ($APIsource === '001') {
            $api_source = "USSD Subscription";
        } elseif ($APIsource === '002') {
            $api_source = "Jazz Application";
        } else {
            // Handle incorrect API source
            return response()->json(['error' => "true", 'messageCode' => 404, 'message' => 'APISource wrong'], 404);
        }


        $product = ProductModel::where('plan_id', $planId)
            ->where('product_id', $product_id)
            ->first();

        // Check if product exists
        if (!$product) {
            return response()->json(['error' => "true", 'messageCode' => 404, 'message' => 'Product not found'], 404);
        }





        $transaction_amount = ProductModel::where('fee', $transaction_amount)
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
                'pulse' => $api_source,
                'api_source' => $api_source,
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

    public function marchant_subscription(Request $request)
    {

                $validator = Validator::make($request->all(), [
                    'plan_id' => 'required|integer',
                    'product_id' => 'required|integer',
                    'customer_msisdn' => 'required|string',
                    'marchant_msisdn' => 'required|string',
                ]);

                // Check if validation fails
                if ($validator->fails()) {
                    return response()->json([
                        'messageCode' => 400,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 400);
                }

                 $customer_msisdn = $request->input("customer_msisdn");

                // Agar number '0' se shuru hota hai, to us '0' ko remove karna hai
                   $customer_msisdn = ltrim($customer_msisdn, '0');

                  // Ab $customer_msisdn use karke further process kar sakte hain
                // Get request parameters
                $planId = $request->input('plan_id');
                $productId = $request->input('product_id');
                $subscriber_msisdn = $customer_msisdn;
	         	$subscriber_msisdn_portal = "0" . $subscriber_msisdn;
	         	$subscriber_msisdn = "92" . $subscriber_msisdn;
                $marchant_msisdn = $request->input("marchant_msisdn");

                // dd($subscriber_msisdn);

                $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn_portal)
                        ->where('plan_id', $planId)
                        ->where('policy_status', 1)
                        ->exists();

                    //$subscription->makeHidden(['created_at', 'updated_at']);

                    if ($subscription) {
                        // Record exists and status is 1 (subscribed)
                    return response()->json([
                            'status' => 'Registered',
                            'data' => [
                                'messageCode' => 2001,
                                'message' => 'Already subscribed to the plan.',
                            ],
                        ], 200);
                    }


                $products = ProductModel::where('plan_id', $planId)
                        ->where('product_id', $productId) // Add this line
                        ->where('status', 1)
                        ->select('fee', 'duration', 'status')
                        ->first();

                if (!$products) {
                    return response()->json([
                        'messageCode' => 500,
                        'message' => 'Product not found or inactive.',
                    ]);
                }

                $fee = $products->fee;
                $duration = $products->duration;


                //Generate a 32-digit unique referenceId
                $referenceId = strval(mt_rand(100000000000000000, 999999999999999999));

                // Additional body parameters
                $type = 'sub';

                // Replace these with your actual secret key and initial vector
                $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
                $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector

                $data = json_encode([
                    'accountNumber' => $subscriber_msisdn,
                    'amount'        => $fee,
                    'referenceId'   => $referenceId,
                    'type'          => $type,
                    'merchantName'  => 'KFC',
                    'merchantID'    => '10254',
                    'merchantCategory' => 'Cellphone',
                    'merchantLocation' => 'Khaadi F-8',
                    'POSID' => '12312',
                    'Remark' => 'This is test Remark',
                    'ReservedField1' => "",
                    'ReservedField2' => "",
                    'ReservedField3' => ""
                ]);

                // echo "Request Plain Data (RPD): $data\n";

                $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

                // Convert the encrypted binary data to hex
                $hexEncryptedData = bin2hex($encryptedData);

                // Output the encrypted data in hex
                //echo "Encrypted Data (Hex): $hexEncryptedData\n";
               $url = 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/sub_autoPayment';

                $headers = [
                    'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
                    'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
                    'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
                    'Content-Type: application/json',
                ];

                $body = json_encode(['data' => $hexEncryptedData]);

                $start = microtime(true);
                $requestTime = now()->format('Y-m-d H:i:s');
                $ch = curl_init($url);

                // Set cURL options
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 180);

                if (curl_errno($ch)) {
                    echo 'Curl error: ' . curl_error($ch);
                }
                // Execute cURL session and get the response
                $response = curl_exec($ch);

                  // Logs
              Log::channel('ussd_api')->info('Marchant Subscription Api.',[
                'url' => $url,
                'request-packet' => $body,
                'response-data' => $response,
                ]);

                // Check for cURL errors
                if ($response === false) {
                    echo 'Curl error: ' . curl_error($ch);
                }

                // Close cURL session
                curl_close($ch);

                // Debugging: Echo raw response
                //echo "Raw Response:\n" . $response . "\n";

                // Handle the response as needed
                $response = json_decode($response, true);
                $end = microtime(true);
                $responseTime = now()->format('Y-m-d H:i:s');
                $elapsedTime = round(($end - $start) * 1000, 2);



                if (isset($response['data'])) {
                    $hexEncodedData = $response['data'];

                    $binaryData = hex2bin($hexEncodedData);

                    // Decrypt the data using openssl_decrypt
                    $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

                    // echo $decryptedData;

                    $data = json_decode($decryptedData, true);

                    $resultCode = $data['resultCode'];
                    $resultDesc = $data['resultDesc'];
                    $transactionId = $data['transactionId'];
                    $failedReason = $data['failedReason'];
                    $amount = $data['amount'];
                    $referenceId = $data['referenceId'];
                    $accountNumber = $data['accountNumber'];


                    //echo $resultCode;
                    if($resultCode == 0)
                    {

                    $customer_id = '0011' . $subscriber_msisdn;
                    //Grace Period
                    $grace_period='14';

                    $current_time = time(); // Get the current Unix timestamp
                    $future_time = strtotime('+14 days', $current_time); // Add 14 days to the current time

                    $activation_time=date('Y-m-d H:i:s');
                    // Format the future time if needed
                    $grace_period_time = date('Y-m-d H:i:s', $future_time);


                    //Recusive Charging Date

                    $future_time_recursive = strtotime("+" . $duration . " days", $current_time);
                    $future_time_recursive_formatted = date('Y-m-d H:i:s', $future_time_recursive);


                    $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn_portal)
                        ->where('plan_id', $planId)
                        ->where('policy_status', 1)
                        ->exists();


                    if ($subscription) {
                        // Record exists and status is 1 (subscribed)

                    return response()->json([
                            'status' => 'Registered',
                            'data' => [
                                'messageCode' => 2001,
                                'message' => 'Already subscribed to the plan.',
                            ],
                        ], 200);
                    }

                    else {

                        $MarchantSubscriptionData = MarchantModel::create([
                            'marchant_msisdn' =>$marchant_msisdn,
                            'customer_msisdn' => $subscriber_msisdn_portal,
                            'amount' =>$fee,
                            'status' => 'success'
                        ]);

                    $CustomerSubscriptionData = CustomerSubscription::create([
                        'marchant_id' => $MarchantSubscriptionData->id,
                        'customer_id'=> $customer_id,
                        'payer_cnic' => -1,
                        'payer_msisdn' => $subscriber_msisdn_portal,
                        'subscriber_cnic' =>-1,
                        'subscriber_msisdn' =>$subscriber_msisdn_portal,
                        'beneficiary_name' =>-1,
                        'beneficiary_msisdn' =>-1,
                        'transaction_amount' =>$fee,
                        'transaction_status' =>1,
                        'referenceId' =>$referenceId,
                        'cps_transaction_id' =>$transactionId,
                        'cps_response_text' =>"Service Activated Sucessfully",
                        'product_duration' =>$duration,
                        'plan_id' =>$planId,
                        'productId' =>$productId,
                        'policy_status' =>1,
                        'pulse' =>"Marchant Api",
                        'api_source' => "Marchant Subscription",
                        'recursive_charging_date' => $future_time_recursive_formatted,
                        'subscription_time' =>$activation_time,
                        'grace_period_time' => $grace_period_time,
                        'sales_agent' => -1,
                        'company_id' =>17
                    ]);

                    $CustomerSubscriptionDataID=$CustomerSubscriptionData->subscription_id;



                            return response()->json([
                            'status' => 'success',
                                'data' => [
                                    'messageCode' => 2002,
                                    'message' => 'Policy subscribed successfully',
                                    'policy_subscription_id' => $CustomerSubscriptionDataID,
                                ],
                            ], 200);

                    }


                    }
                    else
                    {

                        $MarchantSubscriptionData = MarchantModel::create([
                            'marchant_msisdn' => $marchant_msisdn,
                            'customer_msisdn' => $subscriber_msisdn_portal,
                            'amount' => $fee,
                            'reason' => $failedReason,
                            'status' => 'failed'
                        ]);

                         FailedSubscriptionsController::saveFailedTransactionData($transactionId,$resultCode,$resultDesc,$failedReason,$amount,$referenceId,$accountNumber,$planId,$productId,-1,14);
                        return response()->json([
                            'status' => 'Failed',
                            'data' => [
                                'messageCode' => 2003,
                                'message' => $resultDesc,
                            ],
                        ], 422);
                      }
                }
                else
                    {
                     return response()->json([
                            'status' => 'Error',
                            'data' => [
                                'messageCode' => 500,
                                'message' => 'Error In Response from JazzCash Payment Channel',
                            ],
                        ], 500);
                    }




    }





}
