<?php

namespace App\Http\Controllers\bulkmanager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use App\Models\BulkManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class BulkManagerController extends Controller
{
    public function index()
    {
        return view('superadmin.bulkmanager.index');
    }
    public function create()
    {
        return view('superadmin.bulkmanager.create');
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

        // dd($request->all());
        // Validate the request
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        if ($request->hasFile('bulk_file')) {
            // Get the uploaded file
            $file = $request->file('bulk_file');
            // dd($file);
            $filePath = $file->getRealPath();

            // Load the spreadsheet file
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            // dd($sheet);
            $rows = $sheet->toArray();
            //    dd($rows);
            // Extract data
            $extractedData = [];
            // dd($extractedData);
            foreach ($rows as $row) {
                // Assuming 'amount' is in the first column and 'number' is in the second
                $extractedData[] = [
                    'number' => $row[0],
                    'amount' => $row[1],
                ];
            }

            // Process the extracted data (e.g., save to the database)
            foreach ($extractedData as $data) {
                 $msisdn =   $data['number'];
                 $amount  =   $data['amount'];
                //  dd($am);
                  $todayDate = Carbon::now()->toDateString();

                 $subscriptions = CustomerSubscription::where('subscriber_msisdn',$msisdn)
                 ->where('grace_period_time', '>=', $todayDate)
                 ->where('policy_status', 1)->get();

                 $superadmin = session('Superadmin');
                 $username = $superadmin->username;

                 try {
                   foreach($subscriptions as $subscription){
                    $refundResult = $this->refundManager($subscription->cps_transaction_id,$subscription->referenceId );


                    $bulkmanager = BulkManager::create([
                        'subsecribe_id' => $subscription->subscription_id,
                        'msisdn' => $subscription->subscriber_msisdn,
                        'reason' => $refundResult['resultDesc'],
                        ]);

                    if ($refundResult['resultCode'] == 0) {
                        // Call unsubscribeNow function with referenceId and CPS Transaction ID
                        $subscription->update(['policy_status' => 0]);

                        $refundedCustomer=RefundedCustomer::create([
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



                        // Handle $unsubscribeResult as needed
                        return redirect()->back()->with('success', 'Customer unsubscribed successfully.');
                    }

                    else {
                        // Handle the case when refundManager fails
                        return redirect()->back()->with([
                            'error' => 'Refund failed',
                            'resultCode' => $refundResult['resultCode'],
                            'resultDesc' => $refundResult['resultDesc']
                        ], 500);
                     }

                   }
                 }


                  catch (\Exception $e) {
                     return response()->json(['error' => $e->getMessage()], 500);
                 }
                //   dd($subscription);

            }
        }

        return redirect()->back()->with('success', 'Company profile created successfully');
    }

    public function refundManager($originalTransactionId, $referenceId)
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
                'url' => 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub',
		 'request-data' => $data,
                ]);


    //return $data



    $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
    $hexEncryptedData = bin2hex($encryptedData);

    $url = 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub';

    $headers = [
        'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
        'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
        'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
        'Content-Type: application/json',
    ];

    $body = json_encode(['data' => $hexEncryptedData]);

	Log::info('API Request encrypted', [
                'url' => 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub',
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
                'url' => 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub',
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

	 Log::info('API response decrypted', [
                'url' => 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/unsub',
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
