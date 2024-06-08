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

    if ($refundResult['responseCode'] == 0) {
        // Call unsubscribeNow function with referenceId and CPS Transaction ID
        $subscription->update(['policy_status' => 0]);

        $refundedCustomer=RefundedCustomer::create([
        'subscription_id' => $subscription->subscription_id,
        'unsubscription_id' => 2,
        'transaction_id' => $refundResult['transactionID'],
        'reference_id' => $refundResult['referenceID'],
        'cps_response' => $refundResult['responseDescription'],
        'result_description' => $refundResult['responseDescription'],
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
            'resultCode' => $refundResult['responseCode'],
            'resultDesc' => $refundResult['responseDescription']
        ], 500);
     }

}

public function refundManager($originalTransactionId, $referenceId)
{
    $referenceId_new = strval(mt_rand(100000000000000000, 999999999999999999));

    // Replace these with your actual secret key and initial vector
    $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
    $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector

    $data = json_encode([
        'originalTransactionId' => $originalTransactionId,
        'referenceId' => $referenceId_new,
        'POSID' => "12345"
    ]);

    $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
    $hexEncryptedData = bin2hex($encryptedData);

    $tokenUrl = 'https://gateway-sandbox.jazzcash.com.pk/token';
    $apiUrl = 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/autoDebitReversalPayment';

    // Get OAuth token
    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['grant_type' => 'client_credentials']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic T3lYYjhPZE5qQ0pIc25XSGt6bXNsUUFPSlVBYTpVSWUyVDZXWXk2aXhmMmZHZk12WDhScGZ6Z0Fh',
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $tokenResponse = curl_exec($ch);
    if ($tokenResponse === false) {
        return response()->json(['error' => 'Curl error: ' . curl_error($ch)], 500);
    }

    $tokenData = json_decode($tokenResponse, true);
    $accessToken = $tokenData['access_token'] ?? null;

    if (!$accessToken) {
        return response()->json(['error' => 'Failed to retrieve access token'], 500);
    }

    // Close cURL session for token retrieval
    curl_close($ch);

    // Prepare the API request with the retrieved access token
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ];

    $body = json_encode(['data' => $hexEncryptedData]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 180);

    $response = curl_exec($ch);

    if ($response === false) {
        return response()->json(['error' => 'Curl error: ' . curl_error($ch)], 500);
    }

    curl_close($ch);

    $response = json_decode($response, true);

    if (isset($response['data'])) {
        $hexEncodedData = $response['data'];
        $binaryData = hex2bin($hexEncodedData);

        $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $data_1 = json_decode($decryptedData, true);
        //   dd($data_1);
        $resultCode = $data_1['responseCode'];
        // dd($resultCode);
        $resultDesc = $data_1['responseDescription'];

        return $data_1;
    } else {
        return false;
    }
}


}
