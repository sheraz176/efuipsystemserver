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

class USSDSubscriptionController extends Controller
{
    public function getPlans(Request $request)
    {

        $activePlans = PlanModel::select('plan_id', 'plan_name', 'status')->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'data' => $activePlans,
        ])->setStatusCode(200);

    }

    public function getProducts(Request $request)
    {

        $planId = $request->input('plan_id');

      // Retrieve active products associated with the specified plan ID
       $products = ProductModel::where('plan_id', $planId)
                            ->where('status', 1)
                            ->get();

      return response()->json([
        'status' => 'success',
        'data' => $products,
       ])->setStatusCode(200);

    }




    public function ivr_subscription(Request $request)
    {

                $validator = Validator::make($request->all(), [
                    'plan_id' => 'required|integer',
                    'product_id' => 'required|integer',
                    'subscriber_msisdn' => 'required|string',
                ]);

                // Check if validation fails
                if ($validator->fails()) {
                    return response()->json([
                        'messageCode' => 400,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 400);
                }

                // Get request parameters
                $planId = $request->input('plan_id');
                $productId = $request->input('product_id');
                $subscriber_msisdn = $request->input("subscriber_msisdn");
		$subscriber_msisdn_portal = "0" . $subscriber_msisdn;
		$subscriber_msisdn = "92" . $subscriber_msisdn;



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
                $type = 'autoPayment';

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

                $url = 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/sub_autoPayment';

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

                    $CustomerSubscriptionData = CustomerSubscription::create([
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
                        'pulse' =>"Recusive Charging",
                        'api_source' => "USSD Subscription",
                        'recursive_charging_date' => $future_time_recursive_formatted,
                        'subscription_time' =>$activation_time,
                        'grace_period_time' => $grace_period_time,
                        'sales_agent' => -1,
                        'company_id' =>14
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
                         FailedSubscriptionsController::saveFailedTransactionData($transactionId,$resultCode,$resultDesc,$failedReason,$amount,$referenceId,$accountNumber,$planId,$productId,-1,14);
                        return response()->json([
                            'status' => 'Failed',
                            'data' => [
                                'messageCode' => 2003,
                                'message' => $resultDesc,
                            ],
                        ], 422);                    }
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


    public function unsubscribeactiveplan(Request $request)
    {

        // dd($request->all());
        $subscriber_msisdn = $request->input("subscriber_msisdn");
        $subscriptionId = $request->input("id");

        //Get Grace Period Time
	    $subscription = CustomerSubscription::where('policy_status', 1)->where('subscription_id', $subscriptionId)->first();

                 dd($subscription);
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
        'medium' => 'USSD',
        ]);


        CustomerUnSubscription::create([
            'unsubscription_datetime' => now(),
            'medium' => "USSD",
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

}
