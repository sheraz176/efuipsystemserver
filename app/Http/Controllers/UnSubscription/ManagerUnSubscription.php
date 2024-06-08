<?php

namespace App\Http\Controllers\UnSubscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use Illuminate\Support\Facades\Http;

class ManagerUnSubscription extends Controller
{

public function getOAuthToken()
{
    $tokenUrl = 'https://gateway-sandbox.jazzcash.com.pk/token';

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
        throw new \Exception('Curl error: ' . curl_error($ch));
    }

    $tokenData = json_decode($tokenResponse, true);
    curl_close($ch);

    if (!isset($tokenData['access_token'])) {
        throw new \Exception('Failed to retrieve access token');
    }

    return $tokenData['access_token'];
}
public function autoDebitReversalInquiry($accessToken,$subscription)
{
    $apiUrl = 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/autoDebitReversalInquiry';
        //   dd($subscription);

      // Replace these with your actual secret key and initial vector
            $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
            $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector

            $data = json_encode([

                 'receiverMSISDN' =>  $subscription->subscriber_msisdn,
                'amount' => number_format($subscription->transaction_amount, 2, '.', ''),
                 'referenceId' => $subscription->referenceId,


            ]);

           // echo "Request Plain Data (RPD): $data\n";

            $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

            // Convert the encrypted binary data to hex
            $hexEncryptedData = bin2hex($encryptedData);
        $body = json_encode(['data' => $hexEncryptedData]);

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    // dd($response);

    if ($response === false) {
        throw new \Exception('Curl error: ' . curl_error($ch));
    }

    curl_close($ch);
    return json_decode($response, true);
}
public function autoDebitReversalPayment($accessToken,$subscription,$transactionID)
{
    $apiUrl = 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/autoDebitReversalPayment';

     // Replace these with your actual secret key and initial vector
            $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
            $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector

            $data = json_encode([
                   'Init_transactionID' => $transactionID,
                   'referenceId' => $subscription->referenceId,
            ]);

           // echo "Request Plain Data (RPD): $data\n";

            $encryptedData = openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

            // Convert the encrypted binary data to hex
            $hexEncryptedData = bin2hex($encryptedData);
            $body = json_encode(['data' => $hexEncryptedData]);



    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    if ($response === false) {
        throw new \Exception('Curl error: ' . curl_error($ch));
    }

    curl_close($ch);
    return json_decode($response, true);
}

    public function unsubscribeNow($subscriptionId)
   {
    //   dd($subscriptionId);
        $superadmin = session('Superadmin');
    $username = $superadmin->username;

       $subscription = CustomerSubscription::findOrFail($subscriptionId);
    //  dd($subscription);

      $key = 'mYjC!nc3dibleY3k'; // Your secret key
    $iv = 'Myin!tv3ctorjCM@'; // Your initial vector

    try {
        $accessToken = $this->getOAuthToken();

        // Step 2: Auto Debit Reversal Inquiry
        $inquiryResponse = $this->autoDebitReversalInquiry($accessToken,$subscription);
        if (!isset($inquiryResponse['data'])) {
            return response()->json(['error' => 'Failed to get inquiry data'], 500);
        }

       // Decrypt the inquiry response data
        $decryptedInquiryData = $this->decryptData($inquiryResponse['data'], $key, $iv);
        $decodedInquiryData = json_decode($decryptedInquiryData, true);

        //   dd($decodedPaymentData);
        // Step 3: Auto Debit Reversal Payment
        $paymentResponse = $this->autoDebitReversalPayment($accessToken,$subscription,$decodedInquiryData ['transactionID']);

        if (!isset($paymentResponse['data'])) {
            return response()->json(['error' => 'Failed to get payment data'], 500);
        }


        //  dd($decodedInquiryData);
             // Decrypt the payment response data
        $decryptedPaymentData = $this->decryptData($paymentResponse['data'], $key, $iv);

        $decodedPaymentData = json_decode($decryptedPaymentData, true);
        //   dd($decodedPaymentData);



    if ($decodedPaymentData['responseCode'] == 0) {
        // Call unsubscribeNow function with referenceId and CPS Transaction ID
        $subscription->update(['policy_status' => 0]);

        $refundedCustomer=RefundedCustomer::create([
        'subscription_id' => $subscription->subscription_id,
        'unsubscription_id' => 2,
        'transaction_id' => $decodedPaymentData['transactionID'],
        'reference_id' => $decodedPaymentData['referenceID'],
        'cps_response' => $decodedPaymentData['responseDescription'],
        'result_description' => $decodedPaymentData['responseDescription'],
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
            'resultCode' => $decodedPaymentData['responseCode'],
            'resultDesc' => $decodedPaymentData['responseDescription']
        ], 500);
     }


    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }

   }



private function decryptData($hexEncodedData, $key, $iv)
{
    // Convert hex to binary
    $binaryData = hex2bin($hexEncodedData);

    // Decrypt the data
    $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

    return $decryptedData;
}


}
