<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Facades\Validator;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\InterestedCustomers\InterestedCustomer;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\logs;
use Carbon\Carbon;
use App\Models\CheckingRequest;
use App\Models\ConsentNumber;
use Illuminate\Support\Facades\Http;
use App\Models\SMSMsisdn;


class PolicyController extends Controller
{


    public function family_policy_sub_api(Request $request)
    {

        $msisdn = preg_replace('/[^0-9]/', '', $request->subscriber_msisdn);

// Case 1: Agar number 92 se start ho aur 12 digit ka ho
if (substr($msisdn, 0, 2) === '92' && strlen($msisdn) === 12) {
    $msisdn = '0' . substr($msisdn, 2);
}
// Case 2: Agar sirf 10 digit ka ho (e.g. 3008758478)
elseif (strlen($msisdn) === 10) {
    $msisdn = '0' . $msisdn;
}

        // Update request
        $request->merge([
            'subscriber_msisdn' => $msisdn
        ]);

        $validator = Validator::make($request->all(), [
            'subscriber_msisdn' => [
                'required',
                'string',
                'regex:/^0[0-9]{10}$/'
            ],
            'cnic' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'messageCode' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }

        $planId = "4";

        $products = ProductModel::where('plan_id', $planId)
            ->where('fee', "1950")
            ->first();

        if (!$products) {
            return response()->json([
                'messageCode' => 500,
                'message' => 'Product not found or inactive.',
            ]);
        }

        $fee = $products->fee;
        $duration = $products->duration;
        $productId = $products->product_id;

        $current_time = time();
        $future_time = strtotime('+14 days', $current_time);
        $grace_period_time = date('Y-m-d H:i:s', $future_time);

        $future_time_recursive = strtotime("+{$duration} days", $current_time);
        $future_time_recursive_formatted = date('Y-m-d H:i:s', $future_time_recursive);

        // $activation_time = "2025-06-27 16:30:34";

        // Random IDs
        $referenceId = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT)
            . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);

        $transactionId = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT)
            . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $existing = CustomerSubscription::where('subscriber_msisdn', $msisdn)
            ->where('transaction_amount', $fee)
            ->where('policy_status', 1)
            ->first();

        if ($existing) {
            return response()->json([
                'messageCode' => 409,
                'message' => 'Already subscribed to this policy.',
                'sub_msisdn' => $existing->subscriber_msisdn,
            ]);
        }

        $subscription = CustomerSubscription::create([
            'customer_id' => "1",
            'payer_cnic' => "1",
            'payer_msisdn' => $msisdn,
            'subscriber_cnic' => $request->cnic,
            'subscriber_msisdn' => $msisdn,
            'beneficiary_name' => $request->name,
            'beneficiary_msisdn' => $msisdn,
            'transaction_amount' => $fee,
            'transaction_status' => 1,
            'referenceId' => $referenceId,
            'cps_transaction_id' => $transactionId,
            'cps_response_text' => "Service Activated Successfully",
            'product_duration' => $duration,
            'plan_id' => $planId,
            'productId' => $productId,
            'policy_status' => 1,
            'pulse' => "Behbud",
            'api_source' => "Behbud",
            'recursive_charging_date' => $future_time_recursive_formatted,
            'subscription_time' => now(),
            'grace_period_time' => $grace_period_time,
            'sales_agent' => "1",
            'company_id' => "22",
            'consent' => "(DTMF),1",
        ]);

        Log::channel('sms_api')->info('Service Activated Successfully Behbud.', [
            'MobileNo' => $request->subscriber_msisdn,
            'plan' => "Family Health Insurance",
            'plan_id' => $planId,
            'productId' => $productId,
            'amount' => $fee,
            'code' => "2010",
        ]);


            $sms = new SMSMsisdn();
             $sms->msisdn = $request->subscriber_msisdn;
             $sms->plan_id = $planId;
              $sms->product_id = $productId;
             $sms->status = "0";
            $sms->save();


        // âœ… Send SMS only if plan_id = 4
        // if ($planId == 4) {
        //     $plan = PlanModel::where('plan_id', $planId)->where('status', 1)->first();
        //     $product = ProductModel::where('plan_id', $planId)
        //         ->where('product_id', $productId)
        //         ->where('status', 1)->first();

        //     if ($plan && $product) {
        //         $fee = $product->fee;
        //         $plantext = $plan->plan_name;
        //         $link = "https://bit.ly/4gnTEWv";
        //         $sms = "Family Health Insurance: Aap ka bharosa wapis jeetnay ke liye JazzCash ne aapka Family Health Insurance bina kisi charges k activate kardia hai. Claim ke liye call karen: 042-111-333-033";

        //         $url = 'https://api.efulife.com/itssr/its_sendsms';
        //         $headers = [
        //             'Channelcode' => 'ITS',
        //             'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
        //             'Content-Type' => 'application/json',
        //         ];

        //         $subscriber_msisdn = $request->subscriber_msisdn;

        //         $payloads = [

        //             [
        //                 'MobileNo' => $subscriber_msisdn,
        //                 'sender' => 'EFU-LIFE',
        //                 'SMS' => $sms,
        //             ],
        //         ];

        //         foreach ($payloads as $payload) {
        //             try {
        //                 $response = Http::withHeaders($headers)->post($url, $payload);

        //                 if ($response->successful()) {

        //                     Log::channel('sms_api')->info('SMS sent successfully.', [
        //                         'plan' => "Family Health Insurance",
        //                         'MobileNo' => $subscriber_msisdn
        //                     ]);
        //                 } else {
        //                     Log::error("SMS failed", ['response' => $response->body()]);
        //                 }
        //             } catch (\Exception $e) {
        //                 Log::error("SMS send exception", ['error' => $e->getMessage()]);
        //             }
        //         }
        //     }
        // }

        return response()->json([
            'messageCode' => 200,
            'plan' => "Family Health Insurance",
            'message' => 'Subscription created successfully.',
            'data' => $subscription
        ]);
    }


    public function medical_policy_sub_api(Request $request)
    {

        // Clean subscriber_msisdn before validation
        $msisdn = preg_replace('/[^0-9]/', '', $request->subscriber_msisdn);

        if (substr($msisdn, 0, 2) === '92' && strlen($msisdn) === 12) {
            // Starts with 92 (like 92300...)
            $msisdn = '0' . substr($msisdn, 2);
        } elseif (strlen($msisdn) === 10) {
            // Like 300....
            $msisdn = '0' . $msisdn;
        }

        // Update request
        $request->merge([
            'subscriber_msisdn' => $msisdn
        ]);

        $validator = Validator::make($request->all(), [
            'subscriber_msisdn' => 'required|string',
            'amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'messageCode' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }

        $planId = "5";

        $products = ProductModel::where('plan_id', $planId)
            ->where('fee', $request->amount)
            ->where('status', 1)
            ->first();

        if (!$products) {
            return response()->json([
                'messageCode' => 500,
                'message' => 'Product not found or inactive.',
            ]);
        }

        $fee = $products->fee;
        $duration = $products->duration;
        $productId = $products->product_id;

        $current_time = time();
        $future_time = strtotime('+14 days', $current_time);
        $grace_period_time = date('Y-m-d H:i:s', $future_time);

        $future_time_recursive = strtotime("+{$duration} days", $current_time);
        $future_time_recursive_formatted = date('Y-m-d H:i:s', $future_time_recursive);

        $activation_time = "2025-06-27 16:30:34";

        // Random IDs
        $referenceId = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT)
            . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);

        $transactionId = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT)
            . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        $subscription = CustomerSubscription::create([
            'customer_id' => "1",
            'payer_cnic' => "1",
            'payer_msisdn' => $request->amount,
            'subscriber_cnic' => "3660286244227",
            'subscriber_msisdn' => $request->subscriber_msisdn,
            'beneficiary_name' => "jahangirkhan",
            'beneficiary_msisdn' => $request->subscriber_msisdn,
            'transaction_amount' => $fee,
            'transaction_status' => 1,
            'referenceId' => $referenceId,
            'cps_transaction_id' => $transactionId,
            'cps_response_text' => "Service Activated Successfully",
            'product_duration' => $duration,
            'plan_id' => $planId,
            'productId' => $productId,
            'policy_status' => 1,
            'pulse' => "LFDT",
            'api_source' => "LFDT",
            'recursive_charging_date' => $future_time_recursive_formatted,
            'subscription_time' => $activation_time,
            'grace_period_time' => $grace_period_time,
            'sales_agent' => "1",
            'company_id' => "20",
            'consent' => "(DTMF),1",
        ]);

        Log::channel('sms_api')->info('Service Activated Successfully.', [
            'MobileNo' => $request->subscriber_msisdn,
            'plan_id' => $planId,
            'plan' => "Medical Insurance",
            'productId' => $productId,
            'amount' => $fee,
        ]);

        // âœ… Send SMS only if plan_id = 5
        if ($planId == 5) {
            $plan = PlanModel::where('plan_id', $planId)->where('status', 1)->first();
            $product = ProductModel::where('plan_id', $planId)
                ->where('product_id', $productId)
                ->where('status', 1)->first();

            if ($plan && $product) {
                $fee = $product->fee;
                $plantext = $plan->plan_name;
                $link = "https://bit.ly/3MGrSXG";
                $sms = "EFU Medical insurance deta hai Rs 7.5 lakh ka hospitalization cover, unlimited online doctor se mashwara aur Rs 10000 tak ka doctor ki fees, dawai aur lab test ka coverage";

                $url = 'https://api.efulife.com/itssr/its_sendsms';
                $headers = [
                    'Channelcode' => 'ITS',
                    'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
                    'Content-Type' => 'application/json',
                ];

                $subscriber_msisdn = $request->subscriber_msisdn;

                $payloads = [
                    [
                        'MobileNo' => $subscriber_msisdn,
                        'sender' => 'EFU-LIFE',
                        'SMS' => "Dear Customer, youâ€™ve successfully subscribed to {$plantext} for PKR {$fee}/-. T&Cs: {$link}",
                    ],
                    [
                        'MobileNo' => $subscriber_msisdn,
                        'sender' => 'EFU-LIFE',
                        'SMS' => "Aap ka bharosa wapis jeetnay ke liye JazzCash ne aapka Medical Insurance bina kisi charges k activate kardia hai. Claim ke liye call karen: 042111333033",
                    ],
                    [
                        'MobileNo' => $subscriber_msisdn,
                        'sender' => 'EFU-LIFE',
                        'SMS' => "Apki EFU insurance deti phone pe doctor se muft mashwaray ki sahoolat. Abhi hamaray doctor se mashwara lenay k liye dial kary 042111333033",
                    ],
                    [
                        'MobileNo' => $subscriber_msisdn,
                        'sender' => 'EFU-LIFE',
                        'SMS' => $sms,
                    ],
                ];

                foreach ($payloads as $payload) {
                    try {
                        $response = Http::withHeaders($headers)->post($url, $payload);

                        if ($response->successful()) {

                            Log::channel('sms_api')->info('SMS sent successfully.', [
                                'plan' => "Medical Insurance",
                                'MobileNo' => $subscriber_msisdn
                            ]);
                        } else {
                            Log::error("SMS failed", ['response' => $response->body()]);
                        }
                    } catch (\Exception $e) {
                        Log::error("SMS send exception", ['error' => $e->getMessage()]);
                    }
                }
            }
        }

        return response()->json([
            'plan' => "Medical Insurance",
            'messageCode' => 200,
            'message' => 'Subscription created successfully.',
            'data' => $subscription
        ]);
    }


    public function JazzIVR(Request $request)
    {

        // Clean subscriber_msisdn before validation
        $msisdn = preg_replace('/[^0-9]/', '', $request->subscriber_msisdn);

        if (substr($msisdn, 0, 2) === '92' && strlen($msisdn) === 12) {
            // Starts with 92 (like 92300...)
            $msisdn = '0' . substr($msisdn, 2);
        } elseif (strlen($msisdn) === 10) {
            // Like 300....
            $msisdn = '0' . $msisdn;
        }

        // Update request
        $request->merge([
            'subscriber_msisdn' => $msisdn
        ]);

        $validator = Validator::make($request->all(), [
            'subscriber_msisdn' => [
                'required',
                'string',
                'regex:/^0[0-9]{10}$/'
            ],

        ]);
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'subscriber_msisdn' => 'required|string',
        ]);

        $plan_id = 4;
        $product_id = 14;

        Log::channel('sms_api')->info('Campaign-1 Subscription Api.', [
            'plan_id' =>  $plan_id,
            'product_id' => $product_id,
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
        $planId = 4;
        $productId = 14;
        $subscriber_msisdn = $request->input("subscriber_msisdn");

        //dd($subscriber_msisdn);

        $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
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
        Log::channel('sms_api')->info('Campaign-1 Subscription Api.', [
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


                $subscription = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
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
                        'payer_msisdn' => $subscriber_msisdn,
                        'subscriber_cnic' => -1,
                        'subscriber_msisdn' => $subscriber_msisdn,
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
                        'pulse' => "Campaign-1",
                        'api_source' => "Campaign-1 Subscription",
                        'consent' => "DTMF-1",
                        'recursive_charging_date' => $future_time_recursive_formatted,
                        'subscription_time' => $activation_time,
                        'grace_period_time' => $grace_period_time,
                        'sales_agent' => 1,
                        'company_id' => 21
                    ]);

                    $CustomerSubscriptionDataID = $CustomerSubscriptionData->subscription_id;

                    // SMS Code

                    $this->sendJazzSmsNotification($subscriber_msisdn, [
                        "Dear customer, Thank you for your trust. Rs.1 has been deducted for Family Health Insurance from your wallet. Policy T&C https://bit.ly/4gnTEWv",
                        "Apni health expense claim asani se JazzCash app se submit karein ya 042-111-333-033 par call karein. Apna experience hum se zaroor share karein!",
                    ]);



                    // End SMS Code

                    return response()->json([
                        'status' => 'success',
                        'data' => [
                            'messageCode' => 2002,
                            'message' => 'Policy subscribed successfully Campaign-1',
                            'policy_subscription' => $CustomerSubscriptionData,
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



    private function sendJazzSmsNotification($subscriber_msisdn, array $messages)
    {
        $key = 'mYjC!nc3dibleY3k';
        $iv = 'Myin!tv3ctorjCM@';
        $cipher = 'AES-128-CBC';

        // Format MSISDN
        $subscriber_msisdn = ltrim($subscriber_msisdn, '+');
        if (substr($subscriber_msisdn, 0, 2) === '92') {
            // already correct
        } elseif (substr($subscriber_msisdn, 0, 1) === '0') {
            $subscriber_msisdn = '92' . substr($subscriber_msisdn, 1);
        } elseif (strlen($subscriber_msisdn) === 10) {
            $subscriber_msisdn = '92' . $subscriber_msisdn;
        }

        foreach ($messages as $message) {
            $payload = [
                'msisdn' => $subscriber_msisdn,
                'content' => $message,
                'referenceId' => uniqid(),
            ];

            $jsonData = json_encode($payload);
            $encryptedBinary = openssl_encrypt($jsonData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $encryptedHex = bin2hex($encryptedBinary);

            $requestBody = json_encode(['data' => $encryptedHex]);

            $ch = curl_init('https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
                'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
                'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            Log::channel('message_api')->info('Jazz SMS API Campaign-1.', [
                'Link' => "https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification",
                'msisdn' => $subscriber_msisdn,
                'sms' => $message,
                'response' => $response,
                'code' => "Campaign-1",
            ]);
        }
    }
}
