<?php

namespace App\Http\Controllers\SMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsDelivery extends Controller
{
    public function smsDelivery(Request $request)
{
    // Retrieve data from the AJAX request
    $msisdn = $request->input('msisdn');
    $transactionId = $request->input('transactionId');
    $agentId = $request->input('agentId');
    $referenceId = $request->input('referenceId');
    $planID= $request->input('SelectedPlan');
    $amount= $request->input('amount');

   



    // Replace these with your actual secret key and initial vector
    $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
    $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector

    $data = json_encode([
        'msisdn' => $msisdn,
        'content' => "Dear Customer, You have successfully subscribed Term Takaful for Rs.{$amount}. T&Cs: https://bit.ly/439oH0L", 
        'referenceId' => $referenceId,
    ]);

    //return $data

    

    $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
    $hexEncryptedData = bin2hex($encryptedData);

    $url = 'https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification';

    $headers = [
        'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
        'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
        'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
        'Content-Type: application/json',
    ];

    $body = json_encode(['data' => $hexEncryptedData]);

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


    if (isset($response['data'])) {
        $hexEncodedData = $response['data'];
        $binaryData = hex2bin($hexEncodedData);



        // Decrypt the data using openssl_decrypt
        $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

        // Handle the decrypted data as needed
        $data_1 = json_decode($decryptedData, true);

        
         $resultCode = $data_1['resultCode'];
         $resultDesc = $data_1['resultDesc'];

        

        if ($resultCode == 000) {
            return response()->json(['message' => 'SMS delivery initiated successfully', 'resultCode' => $resultCode]);
        } else {
            // Handle the case when the resultCode is not 0 (failed)
            return response()->json(['error' => 'SMS delivery failed', 'resultCode' => $resultCode, 'resultDesc' => $resultDesc], 200);
        }
    } 
    
    else {
        // Handle the case when 'data' is not set in the response
        return response()->json(['error' => 'Invalid response format'], 500);
    }
}

}
