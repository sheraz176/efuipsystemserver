<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\Subscription\CustomerSubscription;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\SMSMsisdn;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class FamilyHealthController extends Controller
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

        $activePlans = PlanModel::select('plan_id', 'plan_name', 'status')
            ->where("plan_id", "4")->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'data' => $activePlans,
        ])->setStatusCode(200);
    }

    public function getProducts(Request $request)
    {

        $planId = "4";

        // Retrieve active products associated with the specified plan ID
        $products = ProductModel::where('plan_id', $planId)
             ->where('product_id',"10")
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ])->setStatusCode(200);
    }


    public function family_ivr_subscription(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
            'product_id' => 'required|integer',
            'subscriber_msisdn' => 'required|string',
        ]);

        Log::channel('ivr_api')->info('Ivr Subscription Api.', [
            'plan_id' =>  $request->input('plan_id'),
            'product_id' => $request->input('product_id'),
            'subscriber_msisdn' => $request->input("subscriber_msisdn"),
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
        $subscriber_msisdn_portal = $request->input("subscriber_msisdn");
        $subscriber_msisdn = "92" . substr($subscriber_msisdn, 1);
        //dd($subscriber_msisdn);

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
            ->where('api_status', 1)
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

        $plan = PlanModel::where('plan_id', $planId)
            ->where('status', 1)
            ->first();
        $plantext = $plan->plan_name;

        //Generate a 32-digit unique referenceId
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

        // Logs
        Log::channel('ivr_api')->info('IVR Family Health Subscription Api.', [
            'subscriber_msisdn' => $request->input("subscriber_msisdn"),
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
            if ($resultCode == 0) {

                $customer_id = '0011' . $subscriber_msisdn;
                //Grace Period
                $grace_period = '14';

                $current_time = time(); // Get the current Unix timestamp
                $future_time = strtotime('+14 days', $current_time); // Add 14 days to the current time

                $activation_time = date('Y-m-d H:i:s');
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
                } else {

                    $CustomerSubscriptionData = CustomerSubscription::create([
                        'customer_id' => $customer_id,
                        'payer_cnic' => -1,
                        'payer_msisdn' => $subscriber_msisdn_portal,
                        'subscriber_cnic' => -1,
                        'subscriber_msisdn' => $subscriber_msisdn_portal,
                        'beneficiary_name' => -1,
                        'beneficiary_msisdn' => -1,
                        'transaction_amount' => $fee,
                        'transaction_status' => 1,
                        'referenceId' => $referenceId,
                        'cps_transaction_id' => $transactionId,
                        'cps_response_text' => "Service Activated Sucessfully",
                        'product_duration' => $duration,
                        'plan_id' => $planId,
                        'productId' => $productId,
                        'policy_status' => 1,
                        'pulse' => "ivr_subscription",
                        'api_source' => "IVR Subscription",
                        'consent' => "DTMF-1",
                        'recursive_charging_date' => $future_time_recursive_formatted,
                        'subscription_time' => $activation_time,
                        'grace_period_time' => $grace_period_time,
                        'sales_agent' => 1,
                        'company_id' => 14
                    ]);

                    $CustomerSubscriptionDataID = $CustomerSubscriptionData->subscription_id;

                    // SMS Code
                     $sms = new SMSMsisdn();
                     $sms->msisdn = $subscriber_msisdn;
                     $sms->plan_id = $planId;
                     $sms->product_id = $productId;
                     $sms->status = "0";
                     $sms->save();
                    // End SMS Code

                    return response()->json([
                        'status' => 'success',
                        'data' => [
                            'messageCode' => 2002,
                            'message' => 'Policy subscribed successfully',
                            'policy_subscription_id' => $CustomerSubscriptionDataID,
                        ],
                    ], 200);
                }
            } else {
                FailedSubscriptionsController::saveFailedTransactionData($transactionId, $resultCode, $resultDesc, $failedReason, $amount, $referenceId, $accountNumber, $planId, $productId, -1, 14);
                return response()->json([
                    'status' => 'Failed',
                    'data' => [
                        'messageCode' => 2003,
                        'message' => $resultDesc,
                    ],
                ], 422);
            }
        } else {
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
