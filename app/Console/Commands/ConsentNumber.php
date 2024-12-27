<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Plans\ProductModel;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\ConsentData;
use App\Models\ConsentNumber as Consent;


class ConsentNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consent:number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consent Number Running';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {



        $consent_numbers = Consent::where('status', 1)
            ->where('consent', '(DTMF),1')->where('response', 'Insufficient balance.')
            ->where('resultCode', '2009')
            ->get();
        //dd($consent_numbers);


        // Iterate over subscriptions
        foreach ($consent_numbers as $consent_number) {
            $msisdn = $consent_number->msisdn;
              //dd($msisdn);
            $msisdnumber = preg_replace('/^92/', '0', $msisdn);
              //dd($msisdnumber);
            $amount = $consent_number->amount;
            $consent = $consent_number->consent;
            $customer_cnic = $consent_number->customer_cnic;
            $beneficinary_name = $consent_number->beneficinary_name;
            $beneficiary_msisdn = $consent_number->beneficiary_msisdn;
            $agent_id = $consent_number->agent_id;
            $company_id = $consent_number->company_id;
            $planId = $consent_number->planId;
            $productId = $consent_number->productId;
            $type = 'sub';
            $referenceId = strval(mt_rand(100000000000000000, 999999999999999999));
            $key = 'mYjC!nc3dibleY3k';
            $iv = 'Myin!tv3ctorjCM@';
            $requestData = json_encode([
                'accountNumber' => $msisdn,
                'amount' => $amount,
                'referenceId' => $referenceId,
                'type' => $type,
                'merchantName' => 'KFC',
                'merchantID' => '10254',
                'merchantCategory' => 'Cellphone',
                'merchantLocation' => 'Khaadi F-8',
                'POSID' => '12312',
                'Remark' => 'This is test Remark',
                'ReservedField1' => '',
                'ReservedField2' => '',
                'ReservedField3' => '',
            ]);
            $encryptedRequestData = openssl_encrypt($requestData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
            $hexEncryptedData = bin2hex($encryptedRequestData);
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

            $response = curl_exec($ch);
            Log::channel('consent_number_api')->info('Consent Number  Api.', [
                'msisdn' => $msisdnumber,
                'url' => $url,
                'request-packet' => $body,
                'response-data' => $response,
            ]);

            if ($response === false) {
                echo 'Curl error: ' . curl_error($ch);
            }

            curl_close($ch);
            $response = json_decode($response, true);
            $end = microtime(true);
            $responseTime = now()->format('Y-m-d H:i:s');
            $elapsedTime = round(($end - $start) * 1000, 2);

            $products = ProductModel::where('plan_id', $planId)
                ->where('product_id', $productId)
                ->where('status', 1)
                ->select('fee', 'duration', 'status')
                ->first();

            if (!$products) {
                continue; // Skip this consent number if the product is not found
            }

            $fee = $products->fee;
            $duration = $products->duration;


            if (isset($response['data'])) {
                $hexEncodedData = preg_replace('/[^0-9a-fA-F]/', '', $response['data']);
                if (strlen($hexEncodedData) % 2 !== 0) {
                    $hexEncodedData = '0' . $hexEncodedData;
                }
                $binaryData = hex2bin($hexEncodedData);
                $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
                $data = json_decode($decryptedData, true);

                if ($data !== null && isset($data['resultCode']) && $data['resultCode'] === "0") {
                    $customer_id = '0011' . $msisdnumber;
                    $activation_time = date('Y-m-d H:i:s');
                    $grace_period_time = date('Y-m-d H:i:s', strtotime('+14 days'));
                    $future_time_recursive_formatted = date('Y-m-d H:i:s', strtotime("+" . $duration . " days"));

                    $subscription = CustomerSubscription::where('subscriber_msisdn', $msisdnumber)
                        ->where('plan_id', $planId)
                        ->where('policy_status', 1)
                        ->exists();
                  //dd($subscription);
                    if (!$subscription) {
                        CustomerSubscription::create([
                            'customer_id' => $customer_id,
                            'payer_msisdn' => $msisdnumber,
                            'subscriber_cnic' => $customer_cnic,
                            'subscriber_msisdn' => $msisdnumber,
                            'beneficiary_name' => $beneficinary_name,
                            'beneficiary_msisdn' => $beneficiary_msisdn,
                              'payer_cnic' => $customer_cnic,
                            'transaction_amount' => $fee,
                            'transaction_status' => 1,
                            'referenceId' => $referenceId,
                            'cps_transaction_id' => $data['transactionId'],
                            'cps_response_text' => "Service Activated Successfully",
                            'product_duration' => $duration,
                            'plan_id' => $planId,
                            'productId' => $productId,
                            'policy_status' => 1,
                            'pulse' => "ConsentCustomer",
                            'api_source' => "AutoDebit",
                            'recursive_charging_date' => $future_time_recursive_formatted,
                            'subscription_time' => $activation_time,
                            'grace_period_time' => $grace_period_time,
                            'sales_agent' => $agent_id,
                            'company_id' => $company_id,
                            'consent' => $consent,
                        ]);
                        $consent_number->status = '0';
                        $consent_number->save();

                             // Create a new ConsentNumber instance
                             $ConsentData = new ConsentData();
                             $ConsentData->msisdn = $msisdnumber;
                             $ConsentData->amount = $amount;
                             $ConsentData->resultCode = $data['resultCode'];
                             $ConsentData->response = $data['resultDesc'];
                             $ConsentData->agent_id = $agent_id;
                             $ConsentData->company_id = $company_id;
                             $ConsentData->planId = $planId;
                             $ConsentData->productId = $productId;
                             $ConsentData->status = "Success";
                             $ConsentData->save();


                    }
                }
                 else if ($data !== null) {
                    FailedSubscriptionsController::saveFailedTransactionDataautoDebit(
                        $data['transactionId'],
                        $data['resultCode'],
                        $data['resultDesc'],
                        $data['failedReason'],
                        $data['amount'],
                        $data['referenceId'],
                        $data['accountNumber'],
                        $planId,
                        $productId,
                        $agent_id,
                        $company_id
                    );
                      // Create a new ConsentNumber instance
                      $ConsentData = new ConsentData();
                      $ConsentData->msisdn = $msisdnumber;
                      $ConsentData->amount = $amount;
                      $ConsentData->resultCode = $data['resultCode'];
                      $ConsentData->response = $data['failedReason'];
                      $ConsentData->agent_id = $agent_id;
                      $ConsentData->company_id = $company_id;
                      $ConsentData->planId = $planId;
                      $ConsentData->productId = $productId;
                      $ConsentData->status = "Failed";
                      $ConsentData->save();

                    $consent_number->count += 1;
                    if ($consent_number->count < 3) {
                        $consent_number->status = '1';
                    } elseif ($consent_number->count == 3) {
                        $consent_number->status = '0';
                    }
                    $consent_number->save();
                }
            }
        }

        $data = ['success' => true, 'message' => 'Recursive charging checked successfully'];
        return json_encode($data);

    }
}
