<?php

namespace App\Http\Controllers\UnSubscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;

class ManagerUnSubscription extends Controller
{
    public function unsubscribeNow($subscriptionId)
{
    $superadmin = session('Superadmin');
    $username = $superadmin->username;

    

    // Fetch the customer subscription record
    $subscription = CustomerSubscription::findOrFail($subscriptionId);

    // Call refundManager function with referenceId and CPSTransaction ID
    $refundResult = $this->refundManager($subscription->cps_transaction_id,$subscription->referenceId );

    
    //return  $refundResult;
    
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

         

        return $data_1;
    } 
    
    else {
        // Handle the case when 'data' is not set in the response
        return false;
    }
}
}
