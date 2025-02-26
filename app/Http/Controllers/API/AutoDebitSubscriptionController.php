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

class AutoDebitSubscriptionController extends Controller
{
    public function AutoDebitSubscription(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
            'product_id' => 'required|integer',
            'subscriber_msisdn' => 'required|string',
            'customer_cnic' => 'required|string', // Add validation rule for customer_cnic
            'beneficiary_msisdn' => 'required|string', // Add validation rule for beneficiary_msisdn
            'beneficiary_cnic' => 'required|string', // Add validation rule for beneficiary_cnic
            'beneficinary_name' => 'required|string', // Add validation rule for beneficinary_name
            'agent_id' => 'required|integer', // Add validation rule for agent_id
            'company_id' => 'required|integer', // Add validation rule for company_id
            // Add validation rules for any other new parameters
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'messageCode' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $today = Carbon::now('Asia/Karachi')->format('Y-m-d');
        $uniqueKey = $request->subscriber_msisdn . '_' . $today; // Generate unique key using MSISDN and today's date

        // Check for existing request with the same unique key (MSISDN + Date)
        $checking_request_number = CheckingRequest::where('unique_key', $uniqueKey)
            ->first();

        // If a record exists for today's request
        if ($checking_request_number) {
            // Check if a request has already been processed (request_number >= 1)
            if ($checking_request_number->request_number >= 1) {
                return response()->json([
                    'status' => 'Failed',
                    'data' => [
                        'messageCode' => 2003,
                        'message' => "Information: The agent has already attempted a deduction for this number. If you are receiving this message, the amount has already been deducted from the customer's account.",
                    ],
                ], 422);
            }

            // Otherwise, proceed to update the request and prevent further hits
            $checking_request_number->is_processing = true; // Set to processing
            $checking_request_number->update();
        } else {
            // If no request exists, create a new one
            $checking_request_number = new CheckingRequest();
            $checking_request_number->msisdn = $request->subscriber_msisdn;
            $checking_request_number->request_number = 0; // Initial request count
            $checking_request_number->unique_key = $uniqueKey; // Use MSISDN + Date
            $checking_request_number->is_processing = true; // Mark as processing
            $checking_request_number->save();
        }

        // Proceed with Jazz system hit if request_number is 0
        if ($checking_request_number->request_number == 0) {
            try {
                // Code to hit the Jazz system...


                // Get request parameters
                $planId = $request->input('plan_id');
                $productId = $request->input('product_id');
                $subscriber_msisdn = $request->input("subscriber_msisdn");
                $subscriber_msisdn_deduction = "92" . ltrim($subscriber_msisdn, '0');

                // Additional parameters sent from frontend
                $customer_cnic = $request->input('customer_cnic');
                $beneficiary_msisdn = $request->input('beneficiary_msisdn');
                $beneficiary_cnic = $request->input('beneficiary_cnic');
                $beneficinary_name = $request->input('beneficinary_name');
                $agent_id = $request->input('agent_id');
                $company_id = $request->input('company_id');
                $consent  = $request->input('consent');
                $super_agent_name = $request->input('super_agent_name');


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

                $plan = PlanModel::where('plan_id', $planId)
                    ->where('status', 1)
                    ->first();
                $plantext = $plan->plan_name;


                //Generate a 32-digit unique referenceId
                $referenceId = strval(mt_rand(100000000000000000, 999999999999999999));

                // Additional body parameters
                $type = 'sub';

                // Replace these with your actual secret key and initial vector
                $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
                $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector

                $data = json_encode([
                    'accountNumber' => $subscriber_msisdn_deduction,
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
                Log::channel('auto_debit_api')->info('Auto Debit Api.', [
                    'Msisdn-number' => $subscriber_msisdn_deduction,
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

                    // Remove non-hexadecimal characters
                    $hexEncodedData = preg_replace('/[^0-9a-fA-F]/', '', $hexEncodedData);
                    // Ensure the length is even
                    if (strlen($hexEncodedData) % 2 !== 0) {
                        $hexEncodedData = '0' . $hexEncodedData;
                    }

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

                    // Logs Table;
                    $logs = logs::create([
                        'msisdn' => $subscriber_msisdn_deduction,
                        'resultCode' => $resultCode,
                        'resultDesc' => $resultDesc,
                        'transaction_id' => $transactionId,
                        'reference_id' =>   $referenceId,
                        'cps_response' => $failedReason,
                        'api_url' => $url,
                        'agent_id' => $request->input('agent_id'),
                        'super_agent_name' => $super_agent_name,
                        'source' => "AutoDebitApi",
                    ]);


                    //echo $resultCode;
                    if ($data !== null && isset($data['resultCode']) && $data['resultCode'] === "0") {

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
                                'subscriber_cnic' => $customer_cnic,
                                'subscriber_msisdn' => $subscriber_msisdn,
                                'beneficiary_name' => $beneficinary_name,
                                'beneficiary_msisdn' => $beneficiary_msisdn,
                                'transaction_amount' => $fee,
                                'transaction_status' => 1,
                                'referenceId' => $referenceId,
                                'cps_transaction_id' => $transactionId,
                                'cps_response_text' => "Service Activated Sucessfully",
                                'product_duration' => $duration,
                                'plan_id' => $planId,
                                'productId' => $productId,
                                'policy_status' => 1,
                                'pulse' => "Recusive Charging",
                                'api_source' => "AutoDebit",
                                'recursive_charging_date' => $future_time_recursive_formatted,
                                'subscription_time' => $activation_time,
                                'grace_period_time' => $grace_period_time,
                                'sales_agent' => $agent_id,
                                'company_id' => $company_id,
                                'consent' => $consent,
                            ]);

                            $CustomerSubscriptionDataID = $CustomerSubscriptionData->subscription_id;

                            $interestedCustomer = InterestedCustomer::where('customer_msisdn', $subscriber_msisdn)
                                ->where('deduction_applied', 0)
                                ->orderBy('id', 'desc') // Order by ID in descending order
                                ->first();
                            // Update deduction_applied to 1 if a matching record is found
                            if ($interestedCustomer) {
                                $interestedCustomer->update(['deduction_applied' => 1]);
                            }

                            // After successful hit, mark request_number to 1
                            $checking_request_number->request_number = 1;
                            $checking_request_number->is_processing = false; // Reset processing flag
                            $checking_request_number->update();

                            // SMS Code
                            $url = 'https://api.efulife.com/itssr/its_sendsms';

                            $plan_id = $plan->plan_id;
                            if ($plan_id == 1) {
                                $link = "https://bit.ly/4d0OYD6";
                            } elseif ($plan_id == 4) {
                                $link = "https://bit.ly/4gnTEWv";
                            } elseif ($plan_id == 5) {
                                $link = "https://bit.ly/3MGrSXG";
                            } else {
                                $link = "https://bit.ly/3KagW3u";
                            }

                            if ($plan_id == 1) {
                                $sms = "EFU Term Life deta hai aapko Rs. 10 lak tak ka life cover, Rs 10000 ka accidental hospitalization aur Rs 2000 tak ka OPD Cover.";
                            } elseif ($plan_id == 4) {
                                $sms = "EFU Family Health Insurance deta hai Rs 5 lakh tak ka family hospitalization cover, C- Section pe Rs 25000, muft doctor se online mashwara aur bohat kuch.";
                            } elseif ($plan_id == 5) {
                                $sms = "EFU Medical insurance deta hai Rs 7.5 lakh ka hospitalization cover, unlimited online doctor se mashwara aur Rs 10000 tak ka doctor ki fees, dawai aur lab test ka coverage";
                            } else {
                                $sms = "EFU Medical insurance deta hai Rs 7.5 lakh ka hospitalization cover, unlimited online doctor se mashwara aur Rs 10000 tak ka doctor ki fees, dawai aur lab test ka coverage";
                            }


                            $payload = [
                                'MobileNo' => $subscriber_msisdn,
                                'sender' => 'EFU-LIFE',
                                'SMS' => "Dear Customer, you’ve successfully subscribed to {$plantext}. for PKR {$fee}/-.T&Cs:{$link} ",
                            ];

                            // Second SMS
                            $payload2 = [
                                'MobileNo' => $subscriber_msisdn,
                                'sender' => 'EFU-LIFE',
                                'SMS' => "Ab claim karna hua nihayat asan. Claim karnay k liye 042111333033 pe call kary ya apnay claim documents support@efulife.com pe email kary.",
                            ];

                              // 3rd SMS
                              $payload3 = [
                                'MobileNo' => $subscriber_msisdn,
                                'sender' => 'EFU-LIFE',
                                'SMS' => "Apki EFU insurance deti phone pe doctor se muft mashwaray ki sahoolat. Abhi hamaray doctor se mashwara lenay k liye dial kary 042111333033",
                               ];

                                  // 4rd SMS
                              $payload4 = [
                                'MobileNo' => $subscriber_msisdn,
                                'sender' => 'EFU-LIFE',
                                'SMS' => "1.Apka Family Health Insurance 2 din mein renew hone wala hai. Baraye karam apne wallet mein kam az kam Rs 199 ki yakeeni banayein taake aap aur aapki family is service se faida utha saky",
                               ];

                                       // 5th SMS
                              $payload5 = [
                                'MobileNo' => $subscriber_msisdn,
                                'sender' => 'EFU-LIFE',
                                'SMS' => $sms,
                               ];

                            $headers = [
                                'Channelcode' => 'ITS',
                                'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
                                'Content-Type' => 'application/json',
                            ];

                            try {
                                // Set timeout for the request (e.g., 5 seconds)
                                $response = Http::withHeaders($headers)->timeout(5)->post($url, $payload);

                                // Optional: Log the response or check for successful response
                                if ($response->successful()) {
                                    Log::info('SMS sent successfully', ['response' => $response->body()]);
                                } else {
                                    Log::warning('SMS API response not successful', ['response' => $response->body()]);
                                }

                                // Send second SMS
                                $response2 = Http::withHeaders($headers)->timeout(5)->post($url, $payload2);
                                if ($response2->successful()) {
                                    Log::info("Second SMS sent successfully", ['MobileNo' => $subscriber_msisdn]);
                                } else {
                                    Log::error("Failed to send second SMS", ['MobileNo' => $subscriber_msisdn, 'Response' => $response2->body()]);
                                }

                                $response3 = Http::withHeaders($headers)->timeout(5)->post($url, $payload3);
                                if ($response3->successful()) {
                                    Log::info("3rd SMS sent successfully", ['MobileNo' => $subscriber_msisdn]);
                                } else {
                                    Log::error("Failed to send second SMS", ['MobileNo' => $subscriber_msisdn, 'Response' => $response3->body()]);
                                }

                                $response4 = Http::withHeaders($headers)->timeout(5)->post($url, $payload4);
                                if ($response4->successful()) {
                                    Log::info("4th SMS sent successfully", ['MobileNo' => $subscriber_msisdn]);
                                } else {
                                    Log::error("Failed to send second SMS", ['MobileNo' => $subscriber_msisdn, 'Response' => $response4->body()]);
                                }

                                $response5 = Http::withHeaders($headers)->timeout(5)->post($url, $payload5);
                                if ($response5->successful()) {
                                    Log::info("5th SMS sent successfully", ['MobileNo' => $subscriber_msisdn]);
                                } else {
                                    Log::error("Failed to send second SMS", ['MobileNo' => $subscriber_msisdn, 'Response' => $response5->body()]);
                                }

                            } catch (\Exception $e) {
                                // Log the exception for debugging
                                Log::error('SMS API call failed', ['error' => $e->getMessage()]);
                            }

                            // End SMS Code

                            return response()->json([
                                'status' => 'success',
                                'data' => [
                                    'messageCode' => 2002,
                                    'message' => 'Policy subscribed successfully <br> Policy ID ' . $CustomerSubscriptionDataID . ' <br> ' . $resultDesc . '<br> This is Your Transaction ID : <br>' . $transactionId,
                                    'policy_subscription_id' => $CustomerSubscriptionDataID,
                                ],
                            ], 200);
                        }
                    } else if ($data !== null) {
                        FailedSubscriptionsController::saveFailedTransactionDataautoDebit($transactionId, $resultCode, $resultDesc, $failedReason, $amount, $referenceId, $accountNumber, $planId, $productId, $agent_id, $company_id);

                        // Create a new ConsentNumber instance
                        $ConsentNumber = new ConsentNumber();
                        $ConsentNumber->msisdn = $accountNumber;
                        $ConsentNumber->amount = $amount;
                        $ConsentNumber->resultCode = $resultCode;
                        $ConsentNumber->response = $resultDesc;
                        $ConsentNumber->consent = $consent;
                        $ConsentNumber->customer_cnic = $customer_cnic;
                        $ConsentNumber->beneficinary_name = $beneficinary_name;
                        $ConsentNumber->beneficiary_msisdn = $beneficiary_msisdn;
                        $ConsentNumber->agent_id = $agent_id;
                        $ConsentNumber->company_id = $company_id;
                        $ConsentNumber->planId = $planId;
                        $ConsentNumber->productId = $productId;
                        $ConsentNumber->status = "1";
                        $ConsentNumber->save();


                        $interestedCustomer = InterestedCustomer::where('customer_msisdn', $subscriber_msisdn)
                            ->where('deduction_applied', 0)
                            ->orderBy('id', 'desc') // Order by ID in descending order
                            ->first();
                        // Update deduction_applied to 1 if a matching record is found
                        if ($interestedCustomer) {
                            $interestedCustomer->update(['deduction_applied' => 1]);
                        }

                        // After successful hit, mark request_number to 1
                        $checking_request_number->request_number = 1;
                        $checking_request_number->is_processing = false; // Reset processing flag
                        $checking_request_number->update();

                        return response()->json([
                            'status' => 'Failed',
                            'data' => [
                                'messageCode' => 2003,
                                'message' => $resultDesc . ' Here is Your Transaction ID: ' . $transactionId,
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

                //End Code to hit the Jazz system...


            } catch (\Exception $e) {
                // Handle errors (rollback is_processing flag)
                $checking_request_number->is_processing = false;
                $checking_request_number->update();

                return response()->json([
                    'status' => 'Failed',
                    'data' => [
                        'messageCode' => 500,
                        'message' => "Error: There was an issue processing the request. Please try again later.",
                    ],
                ], 500);
            }
        } else {
            // If request_number is not 0, no need to hit Jazz system again
            return response()->json([
                'status' => 'Failed',
                'data' => [
                    'messageCode' => 2003,
                    'message' => "Information: The agent has already attempted a deduction for this number.",
                ],
            ], 422);
        }
    }
}
