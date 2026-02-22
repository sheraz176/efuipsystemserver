<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\interestedCustomerData;
use App\Models\Subscription\CustomerSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\Subscription\FailedSubscription;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InterestedCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interested:customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

              // Get today's date in 'YYYY-MM-DD' format
              $today = Carbon::now()->toDateString();

              // Query subscriptions with recursive charging date due today and policy_status = 1

                  $interested_customers = interestedCustomerData::whereDate('created_at', Carbon::today())
                  ->get();

                //   dd($interested_customers);

              // Iterate over subscriptions
              foreach ($interested_customers as $interested_customer) {



                  $msisdn= $interested_customer->subscriber_msisdn;
                  $amount =$interested_customer->amount;

                       // Generate a unique reference ID
            $referenceId = strval(mt_rand(100000000000000000, 999999999999999999));
            $key = 'mYjC!nc3dibleY3k'; // Change this to your secret key
            $iv = 'Myin!tv3ctorjCM@'; // Change this to your initial vector
            // Construct the request data
            $requestData = json_encode([
                'accountNumber' => $msisdn,
                'amount' => $amount,
                'referenceId' => $referenceId,
                'type' => 'autoPayment',
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

            // Encrypt the request data (You need to implement this function)
            $encryptedRequestData = openssl_encrypt($requestData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

              // Convert the encrypted binary data to hex
              $hexEncryptedData = bin2hex($encryptedRequestData);
            // Set up the request parameters
            $url = 'https://gateway-sandbox.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/sub_autoPayment';

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
       Log::channel('interested_customer_api')->info('Interested Customer Api.',[
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

         // Debugging: Echo raw response
         //echo "Raw Response:\n" . $response . "\n";

         // Handle the response as needed
         $response = json_decode($response, true);
         $end = microtime(true);
         $responseTime = now()->format('Y-m-d H:i:s');
         $elapsedTime = round(($end - $start) * 1000, 2);
          // return $response['data'];
                      // Process payment response
                      if (isset($response['data'])) {

                          $hexEncodedData = $response['data'];

                          $binaryData = hex2bin($hexEncodedData);

                          // Decrypt the data using openssl_decrypt
                          $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

                          // echo $decryptedData;

                          $data = json_decode($decryptedData, true);
                        //   dd($data);

                      if ($data['resultCode'] === "0") {
                        //   dd($data);

                          $product = ProductModel::where('product_id',$interested_customer->product_id)->first();
                             $duration  = $product->duration;
                        //   dd($duration);
                          $customer_id = '0011' . $interested_customer->subscriber_msisdn;
                          //Grace Period
                          $grace_period='14';

                          $current_time = time(); // Get the current Unix timestamp
                          $future_time = strtotime('+14 days', $current_time); // Add 14 days to the current time

                          $activation_time=date('Y-m-d H:i:s');
                          // Format the future time if needed
                          $grace_period_time = date('Y-m-d H:i:s', $future_time);


                          //Recusive Charging Date

                          $future_time_recursive = strtotime("+" . $duration . " days", $current_time);
                          $future_time_recursive_formatted = date('Y-m-d H:i:s', $future_time_recursive);


                           // Update database records
                           $CustomerSubscriptionData = CustomerSubscription::create([
                            'customer_id'=> $customer_id,
                            'payer_cnic' => -1,
                            'payer_msisdn' => $interested_customer->subscriber_msisdn,
                            'subscriber_cnic' =>$interested_customer->customer_cnic,
                            'subscriber_msisdn' =>$interested_customer->subscriber_msisdn,
                            'beneficiary_name' =>$interested_customer->beneficiary_name,
                            'beneficiary_msisdn' =>$interested_customer->beneficiary_msisdn,
                            'transaction_amount' =>!empty($data['amount'])?$data['amount']: null,
                            'transaction_status' =>1,
                            'referenceId' =>!empty($data['referenceId'])?$data['referenceId']: null,
                            'cps_transaction_id' =>!empty($data['transactionId']) ?$data['transactionId']: null,
                            'cps_response_text' =>"Service Activated Sucessfully",
                            'product_duration' =>$duration,
                            'plan_id' =>$interested_customer->plan_id,
                            'productId' =>$interested_customer->product_id,
                            'policy_status' =>1,
                            'pulse' =>"Interested Customer Deduction",
                            'api_source' => "AutoDebit",
                            'recursive_charging_date' => $future_time_recursive_formatted,
                            'subscription_time' =>$activation_time,
                            'grace_period_time' => $grace_period_time,
                            'sales_agent' => $interested_customer->agent_id,
                            'company_id' =>$interested_customer->company_id,
                        ]);
                             // Insert payment data into recusive_charging_data table

                              // dd($recusive_charging_data);

                      } else {


                        //   dd($data);
                          // Increment consecutive failure count
                          $FailedSubscriptions = FailedSubscription::create([
                            'transactionId'=> !empty($data['transactionId']) ?$data['transactionId']: null,
                            'timeStamp' =>  !empty($data['timeStamp']) ?$data['timeStamp']: null,
                            'resultCode' =>  !empty($data['resultCode']) ?$data['resultCode']: null,
                            'resultDesc' =>  !empty($data['resultDesc']) ?$data['resultDesc']: null,
                            'failedReason' =>  !empty($data['failedReason']) ?$data['failedReason']: null,
                            'amount' =>  !empty($data['amount']) ?$data['amount']: null,
                            'referenceId' =>  !empty($data['referenceId']) ?$data['referenceId']: null,
                            'accountNumber' => !empty($data['accountNumber']) ?$data['accountNumber']: null,
                            'type' =>  !empty($data['type']) ?$data['type']: null,
                            'remark' =>  !empty($data['remark']) ?$data['remark']: null,
                            'planId' =>$interested_customer->plan_id,
                            'product_id' =>$interested_customer->product_id,
                            'agent_id' =>$interested_customer->agent_id,
                            'company_id' =>$interested_customer->company_id,
                            'source' =>  "portal",
                            'sale_request_time' => !empty($data['timeStamp']) ?$data['timeStamp']: null,


                        ]);

                            // Update date records



                          // dd($recusive_charging_data);
                      }



                  }

              }

              $data = array('success' => true, 'message' => 'Interested Customer checked successfully');
              return json_encode($data);



        return 0;
    }
}
