<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

class AgentBulkManagerController extends Controller
{
    public function index()
    {
        return view('agent.bulkmanager.index');
    }
    public function create()
    {
        return view('agent.bulkmanager.create');
    }
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Start building the query
            $query = BulkManager::select('*');


            return Datatables::of($query)->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($request->hasFile('bulk_file')) {
            // Get the uploaded file
            $file = $request->file('bulk_file');
            $filePath = $file->getRealPath();

            // Load the spreadsheet file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Extract data
            $extractedData = [];
            foreach ($rows as $row) {
                // Assuming 'amount' is in the first column and 'number' is in the second
                $extractedData[] = [
                    'number' => $row[0],
                    'amount' => $row[1],
                ];
            }

            $successCount = 0;
            $errorMessages = [];

            // Process the extracted data (e.g., save to the database)
            foreach ($extractedData as $data) {
                $msisdn = $data['number'];
                $amount = $data['amount'];
                $todayDate = Carbon::now()->toDateString();

                $subscriptions = CustomerSubscription::where('subscriber_msisdn', $msisdn)
                    ->where('grace_period_time', '>=', $todayDate)
                    ->where('policy_status', 1)
                    ->get();

                 if ($subscriptions->isEmpty()) {
                    //  dd('by');
                    return redirect()->back()->with('erroring', "Subscription Not Found.");
                }
                else{

                $superadmin = session('Superadmin');
                $username = $superadmin->username;

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
                            // Call unsubscribeNow function with referenceId and CPS Transaction ID
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

                            $successCount++;
                        } else {
                            $errorMessages[] = "Refund failed for MSISDN: $msisdn - " . $refundResult['resultDesc'];
                        }
                    } catch (\Exception $e) {
                        $errorMessages[] = "Error processing MSISDN: $msisdn - " . $e->getMessage();
                    }
                }
            }

            // Redirect with results after processing all data
            if ($successCount > 0) {
                return redirect()->route('agent.builkmanager.index')->with('success', "customers unsubscribed successfully.");
            } else {
                return redirect()->route('agent.builkmanager.index')->with('error', "Invaild Api Response.");
            }
        }

        return redirect()->route('agent.builkmanager.index')->with('error', 'No file uploaded.');
        }
    }


    public function refundManager($originalTransactionId, $referenceId ,$subscriber_msisdn)
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
