<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\Subscription\CustomerSubscription;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Models\Client;
use App\Models\logs;

class BulkSubscription extends Command
{
    protected $signature = 'bulk:sub';

    protected $description = 'Bulk Sub Run ';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $directory = 'Subbulkfiles'; // Directory for new files
        $processedDirectory = 'SubProcessed'; // Directory for processed files
        $files = Storage::files($directory);

        foreach ($files as $file) {
            $fileName = basename($file);

            // Check if the file has already been processed
            if (Storage::exists($processedDirectory . '/' . $fileName)) {
                $this->info("File $fileName has already been  Sub processed.");
                continue; // Skip this file
            }

            $filePath = storage_path('app/' . $file);
            $fileHandle = fopen($filePath, 'r');

            while (($row = fgetcsv($fileHandle, 1000, ',')) !== false) {
                $msisdn = $row[0];
                $amount = $row[1];
                $todayDate = Carbon::now()->toDateString();

                // Remove the country code '92' and add leading zero '0'
                if (substr($msisdn, 0, 2) == '92') {
                    $msisdn = '0' . substr($msisdn, 2);
                }

                $products = ProductModel::where('fee', $amount)
                    ->where('status', 1)
                    ->get();
                foreach ($products as $product) {


                    $fee = $product->fee;
                    $duration = $product->duration;
                    $planId = $product->plan_id;
                    $productId =  $product->product_id;


                    $subscription = CustomerSubscription::where('subscriber_msisdn', $msisdn)
                        ->where('plan_id', $planId)
                        ->where('policy_status', 1)
                        ->exists();


                    if ($subscription) {

                        $logs = logs::create([
                            'msisdn' => $msisdn,
                            'resultDesc' => "Already subscribed to the plan",
                            'source' => "subbulkapi",
                        ]);
                    } else {
                        $referenceId = strval(mt_rand(100000000000000000, 999999999999999999));
                        $type = 'sub';
                        $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
                        $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector


                        $data = json_encode([
                            'accountNumber' => $msisdn,
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
                        Log::channel('bulk_sub_api')->info('Bulk Sub Api.', [
                            'Msisdn-number' => $msisdn,
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
                        $response = json_decode($response, true);
                        $end = microtime(true);
                        $responseTime = now()->format('Y-m-d H:i:s');
                        $elapsedTime = round(($end - $start) * 1000, 2);
                    }

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
                            'msisdn' => $msisdn,
                            'resultCode' => $resultCode,
                            'resultDesc' => $resultDesc,
                            'transaction_id' => $transactionId,
                            'reference_id' =>   $referenceId,
                            'cps_response' => $failedReason,
                            'api_url' => $url,
                            'agent_id' => '1000',
                            'source' => "subbulkapi",
                        ]);





                        //echo $resultCode;
                        if ($data !== null && isset($data['resultCode']) && $data['resultCode'] === "0") {

                            $customer_id = '0011' . $msisdn;
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


                            $subscription = CustomerSubscription::where('subscriber_msisdn', $msisdn)
                                ->where('plan_id', $planId)
                                ->where('policy_status', 1)
                                ->exists();


                            if ($subscription) {
                                // Record exists and status is 1 (subscribed)

                                $logs = logs::create([
                                    'msisdn' => $msisdn,
                                    'resultDesc' => "Already subscribed to the plan",
                                    'source' => "subbulkapi",
                                ]);
                            } else {

                                $CustomerSubscriptionData = CustomerSubscription::create([
                                    'customer_id' => $customer_id,
                                    'payer_cnic' => -1,
                                    'payer_msisdn' => $msisdn,
                                    'subscriber_cnic' => '00000000000',
                                    'subscriber_msisdn' => $msisdn,
                                    'beneficiary_name' => 'Bulk Sub Api',
                                    'beneficiary_msisdn' => $msisdn,
                                    'transaction_amount' => $fee,
                                    'transaction_status' => 1,
                                    'referenceId' => $referenceId,
                                    'cps_transaction_id' => $transactionId,
                                    'cps_response_text' => "Service Activated Sucessfully",
                                    'product_duration' => $duration,
                                    'plan_id' => $planId,
                                    'productId' => $productId,
                                    'policy_status' => 1,
                                    'pulse' => "IVR Subscription",
                                    'api_source' => "Bulk IVR Subscription",
                                    'recursive_charging_date' => $future_time_recursive_formatted,
                                    'subscription_time' => $activation_time,
                                    'grace_period_time' => $grace_period_time,
                                    'sales_agent' => 1,
                                    'company_id' => 15,

                                ]);

                                $CustomerSubscriptionDataID = $CustomerSubscriptionData->subscription_id;

                                $this->info('Policy subscribed successfully');
                            }
                        } else if ($data !== null) {
                            $agent_id = 1;
                            $company_id = 15;
                            FailedSubscriptionsController::saveFailedTransactionDataautoDebit($transactionId, $resultCode, $resultDesc, $failedReason, $amount, $referenceId, $accountNumber, $planId, $productId, $agent_id, $company_id);
                            $this->info('Failed ');
                        }
                    } else {
                        $this->info('Failed all ');
                    }
                }
            }
            fclose($fileHandle);
            Storage::move($file, $processedDirectory . '/' . $fileName);
        }
    }
}
