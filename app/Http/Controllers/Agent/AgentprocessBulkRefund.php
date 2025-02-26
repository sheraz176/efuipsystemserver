<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use App\Models\BulkManager;
use App\Models\logs;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class AgentprocessBulkRefund extends Controller
{



public function bilkulfileRun(Request $request)
{
    $directory = 'bulkfiles'; // Directory for new files
    $processedDirectory = 'processed'; // Directory for processed files

    try {
        // Check if the directory exists and has files
        if (!Storage::exists($directory)) {
            return response()->json(['status' => 'error', 'message' => 'Directory not found: ' . $directory]);
        }

        $files = Storage::files($directory);

        if (empty($files)) {
            return response()->json(['status' => 'error', 'message' => 'No files found in the directory.']);
        }

        // If files exist, process the bulk refund
        $this->handle();

        return response()->json(['status' => 'success', 'message' => 'Bulk refund processed successfully.']);
    } catch (\Exception $e) {
        \Log::error('Bulk refund processing failed: ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'Failed to process bulk refund: ' . $e->getMessage()]);
    }
}


   public function processfile()
   {

    return view('agent.bulkmanager.proceesBulk');
   }

   public function getProcessedResults()
{
    $processedResults = session()->get('processed_results', []);
    return response()->json($processedResults);
}

public function handle()
{
    $directory = 'bulkfiles'; // Directory for new files
    $processedDirectory = 'processed'; // Directory for processed files
    $files = Storage::files($directory);

    $processedResults = []; // Array to store results

    foreach ($files as $file) {
        $fileName = basename($file);

        if (Storage::exists($processedDirectory . '/' . $fileName)) {
            continue;
        }

        $filePath = storage_path('app/' . $file);
        $fileHandle = fopen($filePath, 'r');

        while (($row = fgetcsv($fileHandle, 1000, ',')) !== false) {
            $msisdn = $row[0];
            $amount = $row[1];
            $todayDate = Carbon::now()->toDateString();

            if (substr($msisdn, 0, 2) == '92') {
                $msisdn = '0' . substr($msisdn, 2);
            }

            $subscriptions = CustomerSubscription::where('subscriber_msisdn', $msisdn)
                ->where('transaction_amount', $amount)
                ->where('grace_period_time', '>=', $todayDate)
                ->where('policy_status', 1)
                ->get();

            if ($subscriptions->isEmpty()) {
                $processedResults[] = [
                    'msisdn' => $msisdn,
                    'status' => 'Subscription Not Found'
                ];

                $logs = logs::create([
                    'msisdn' => $msisdn,
                    'cps_response' => "Subscription Not Found for MSISDN",
                    'source' => "BulkRefundManager",
                    ]);
                continue;
            }
            $username = "Danish2024";

            foreach ($subscriptions as $subscription) {
                try {
                    $refundResult = $this->refundManager(
                        $subscription->cps_transaction_id,
                        $subscription->referenceId,
                        $subscription->subscriber_msisdn
                    );

                    BulkManager::create([
                        'subsecribe_id' => $subscription->subscription_id,
                        'msisdn' => $subscription->subscriber_msisdn,
                        'reason' => $refundResult['resultDesc'],
                    ]);

                    if ($refundResult['resultCode'] == 0) {
                        $subscription->update(['policy_status' => 0]);

                        $refundedCustomer = RefundedCustomer::create([
                            'subscription_id' => $subscription->subscription_id,
                            'unsubscription_id' => 2,
                            'transaction_id' => $refundResult['transactionId'],
                            'reference_id' => $refundResult['referenceId'],
                            'cps_response' => $refundResult['failedReason'],
                            'result_description' => $refundResult['resultDesc'],
                            'result_code' => 0,
                            'refunded_by' => $username,
                            'medium' => 'Portal',
                        ]);

                        CustomerUnSubscription::create([
                            'unsubscription_datetime' => now(),
                            'medium' => "portal",
                            'subscription_id' => $subscription->subscription_id,
                            'refunded_id' => $refundedCustomer->refund_id,
                        ]);
                        $processedResults[] = [
                            'msisdn' => $msisdn,
                            'status' => 'Refund Successful'
                        ];

                        $this->info("Refund processed successfully for MSISDN: $msisdn.");
                    }

                    else {
                        $processedResults[] = [
                            'msisdn' => $msisdn,
                            'status' => 'Refund Failed: ' . $refundResult['resultDesc']
                        ];
                    }
                } catch (\Exception $e) {

                }
            }
        }

        fclose($fileHandle);
        Storage::move($file, $processedDirectory . '/' . $fileName);
    }

    // Save results to session
    session()->put('processed_results', $processedResults);
}


private function refundManager($originalTransactionId, $referenceId ,$subscriber_msisdn)
{


    $referenceId_new = strval(mt_rand(100000000000000000, 999999999999999999));
    // Retrieve data from the AJAX request
    //dd($originalTransactionId,$referenceId);
    // Replace these with your actual secret key and initial vector
    $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
    $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector

    $data = json_encode([
        'originalTransactionId' => $originalTransactionId,
        'referenceId' =>  $referenceId_new,
        'POSID' => "12345"
    ]);

    Log::info('API Request', [
                'url' => 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub',
         'request-data' => $data,
                ]);


    //return $data



    $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
    $hexEncryptedData = bin2hex($encryptedData);

    $url = 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub';

    $headers = [
        'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
        'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
        'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
        'Content-Type: application/json',
    ];

    $body = json_encode(['data' => $hexEncryptedData]);

    Log::info('API Request encrypted', [
                'url' => 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub',
         'request-encrypted-data' => $hexEncryptedData,
                ]);


    //return $body;

    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 180);

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        return response()->json(['error' => 'Curl error: ' . curl_error($ch)], 500);
    }

    // Close cURL session
    curl_close($ch);

    // Debugging: Echo raw response
    // echo "Raw Response:\n" . $response . "\n";

    // Handle the response as needed
    $response = json_decode($response, true);

    Log::info('API response encrypted', [
                'url' => 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub',
         'response-encrypted-data' => $response,
                ]);




    if (isset($response['data'])) {
        $hexEncodedData = $response['data'];
        $binaryData = hex2bin($hexEncodedData);



        // Decrypt the data using openssl_decrypt
        $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

        // Handle the decrypted data as needed
        $data_1 = json_decode($decryptedData, true);


         $resultCode = $data_1['resultCode'];
         $resultDesc = $data_1['resultDesc'];
         $transaction_id = $data_1['transactionId'];
        //  $reference_id =   $data_1['referenceId'];
         $cps_response = $data_1['failedReason'];
         $msisdn = $subscriber_msisdn;
         $response_encrypted_data = $response;
         $response_decrypted_data = $decryptedData;
         $api_url = "https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub";

         $logs = logs::create([
            'msisdn' => $msisdn,
            'resultCode' => $resultCode,
            'resultDesc' => $resultDesc,
            'transaction_id' => $transaction_id,
            'reference_id' =>   $referenceId,
            'cps_response' => $cps_response,
            'api_url' => $api_url,
            'source' => "BulkRefundManager",
            ]);


     Log::info('API response decrypted', [
                'url' => 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub',
         'response-encrypted-data' => $decryptedData,
                ]);





         return $data_1;
    }

    else {
        // Handle the case when 'data' is not set in the response
        return false;
    }
}

}
