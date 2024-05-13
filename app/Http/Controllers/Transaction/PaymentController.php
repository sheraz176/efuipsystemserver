<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Facades\Log;
use App\Models\CheckingRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller

{

    public function transactionController(Request $request)
    {
        $today = Carbon::today();
        $checking_request_number  = CheckingRequest::where('msisdn',$request->msisdn)
        ->whereDate('created_at', $today)->first();
    //    dd($checking_request_number);

     if (!empty($checking_request_number->msisdn)) {
         $checking_number = $checking_request_number->msisdn;
      }
      else{
        $checking_number="";
      }
        //  dd($checking_number);
          if(!empty($request->msisdn == $checking_number)){
            //   dd('hi');
            if ($checking_request_number->request_number < 5) {
              $checking_request = CheckingRequest::find($checking_request_number->id);
              $checking_request->request_number++;
              $checking_request->update();
            }

          }
          else{
            // dd('by');
                $checking_request   = new CheckingRequest();
                $checking_request->msisdn = $request->msisdn;
                $checking_request->request_number = "1";
                $checking_request->save();
                // dd($checking_request);
          }
          $checking_request_count  = CheckingRequest::where('msisdn',$request->msisdn)
          ->whereDate('created_at', $today)->first();
      if (!empty($checking_request_count->request_number < 5)) {
        try {
            // Get request parameters
            $msisdn = $request->input('msisdn');
            $amount = $request->input('amount');
            $duration = $request->input('duration');
            $agent_id = $request->input('agent_id');
            $company_id = $request->input('company_id');
            $product_id = $request->input('productID');
            $planID = $request->input('planID');
            $Beneficinary_name = $request->input('beneficiary_name');



            // Generate a 32-digit unique referenceId
            $referenceId = strval(mt_rand(100000000000000000, 999999999999999999));

            // Additional body parameters
            $type = 'sub';

            // Replace these with your actual secret key and initial vector
            $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
            $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector

            $data = json_encode([
                'accountNumber' => $msisdn,
                'amount'        => $amount,
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
            Log::info('API Request', [
                'url' => 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/sub_autoPayment',
                'request_time' => $requestTime,
                'response_time' => $responseTime,
                'elapsed_time' => $elapsedTime . 'ms',
                'response' =>  $response,
		        'request-data' => $hexEncryptedData,
                ]);

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

                //dd($resultCode);

                //echo $resultCode;
                if($resultCode == 0)
                {
                    SubscriptionController::saveSubscriptionData($msisdn,$amount,$transactionId,$referenceId, $duration,$agent_id, $planID,$product_id,$resultCode,$resultDesc,$failedReason,$Beneficinary_name,$company_id);

                    return response()->json($data, 200);
                }
                else
                {
                    FailedSubscriptionsController::saveFailedTransactionData($transactionId,$resultCode,$resultDesc,$failedReason,$amount,$referenceId,$accountNumber,$planID,$product_id,$agent_id,$company_id);
                    return response()->json($data, 200);
                }


            }

            else {
                return response()->json([
                    'errorjs' => 'Timeout & Delayed Response from JazzCash', // Custom error code
                    'error' => 'Kindly Check Your Internet Connection, There is Timeout Error Appearing While Requesting the M-PIN Request',
                ], 500);
               }






        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    else{

        return response()->json([
            'errorjs' => 'MPIN Request Limit Exceed', // Custom error code
            'error' => 'As Per Jazzcash, You Have Exceeds the Number of M-PINs Request to This Number, Kindly Try Again After 24 Hours.',
        ], 500);
                //  dd('request limited exited');
    }


    }





}
