<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\RecusiveCharging as RecusiveChargingModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class RecusiveCharging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recusive:charging';

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
            $subscriptions = DB::table('customer_subscriptions')
                ->select('subscription_id', DB::raw("CONCAT('92', SUBSTRING(subscriber_msisdn, -10)) AS subscriber_msisdn"), 'transaction_amount', 'consecutiveFailureCount', 'recursive_charging_date', 'product_duration', 'plan_id', 'productId')
                ->whereDate('recursive_charging_date', $today)
                ->where('policy_status', 1)->whereIn('transaction_amount',[4, 133])
                ->get();
              //dd($subscriptions);
            // Iterate over subscriptions
            foreach ($subscriptions as $subscription) {



                $msisdn= $subscription->subscriber_msisdn;
                $amount =$subscription->transaction_amount;

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
        // return $response['data'];
                    // Process payment response
                    if (isset($response['data'])) {

                        $hexEncodedData = $response['data'];

                        $binaryData = hex2bin($hexEncodedData);

                        // Decrypt the data using openssl_decrypt
                        $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

                        // echo $decryptedData;

                        $data = json_decode($decryptedData, true);
                    //  dd($data);

                    if ($data['resultCode'] === "0") {
                        // dd($data);
                        // Calculate next charging date
                        $nextChargingDate = Carbon::parse($subscription->recursive_charging_date)->addDays($subscription->product_duration)->toDateString();

                         // Update database records
                         DB::table('customer_subscriptions')
                         ->where('subscription_id', $subscription->subscription_id)
                         ->update([
                             'recursive_charging_date' => $nextChargingDate,
                             'consecutiveFailureCount' => 0
                         ]);
                           // Insert payment data into recusive_charging_data table
                           $recusive_charging_data   = new RecusiveChargingModel();
                           $recusive_charging_data->subscription_id = $subscription->subscription_id;
                           $recusive_charging_data->tid = !empty($data['transactionId']) ?$data['transactionId']: null;
                           $recusive_charging_data->reference_id = !empty($data['referenceId'])?$data['referenceId']: null;
                           $recusive_charging_data->amount = !empty($data['amount'])?$data['amount']: null;
                           $recusive_charging_data->plan_id = $subscription->plan_id;
                           $recusive_charging_data->product_id = $subscription->productId;
                           $recusive_charging_data->cps_response = !empty($data['resultDesc'])?$data['resultDesc']: $data['failedReason'];
                           $recusive_charging_data->charging_date = $nextChargingDate;
                           $recusive_charging_data->customer_msisdn = $subscription->subscriber_msisdn;
                           $recusive_charging_data->duration = $subscription->product_duration;
                           $recusive_charging_data->save();

                            // dd($recusive_charging_data);

                    } else {
                        // dd($data);
                        // Increment consecutive failure count
                        DB::table('customer_subscriptions')
                            ->where('subscription_id', $subscription->subscription_id)
                            ->increment('consecutiveFailureCount');

                        // Update policy status if consecutive failure count reaches 6
                        if ($subscription->consecutiveFailureCount === 6) {
                            DB::table('customer_subscriptions')
                                ->where('subscription_id', $subscription->subscription_id)
                                ->update(['policy_status' => 0]);
                        }

                          // Update date records
                        $nextChargingDate = Carbon::parse($subscription->recursive_charging_date)->addDays($subscription->product_duration)->toDateString();                       
                        DB::table('customer_subscriptions')
                        ->where('subscription_id', $subscription->subscription_id)
                        ->update([
                            'recursive_charging_date' => $nextChargingDate,
                            'consecutiveFailureCount' => 0
                        ]);

                        // Insert payment data into recusive_charging_data table

                        $recusive_charging_data   = new RecusiveChargingModel();
                        $recusive_charging_data->subscription_id = $subscription->subscription_id;
                        $recusive_charging_data->tid = !empty($data['transactionId']) ?$data['transactionId']: null;
                        $recusive_charging_data->reference_id = !empty($data['referenceId'])?$data['referenceId']: null;
                        $recusive_charging_data->amount = !empty($data['amount'])?$data['amount']: null;
                        $recusive_charging_data->plan_id = $subscription->plan_id;
                        $recusive_charging_data->product_id = $subscription->productId;
                        $recusive_charging_data->cps_response = !empty($data['resultDesc'])?$data['resultDesc']: $data['failedReason'];
                        $recusive_charging_data->charging_date = $nextChargingDate;
                        $recusive_charging_data->customer_msisdn = $subscription->subscriber_msisdn;
                        $recusive_charging_data->duration = $subscription->product_duration;
                        $recusive_charging_data->save();
                        // dd($recusive_charging_data);
                    }
                }
                // else
                // {
                //     $data = array('success' => false, 'message' => 'Error In Response from JazzCash Payment Channel');
                //     return json_encode($data);
                // }
            }

            $data = array('success' => true, 'message' => 'Recursive charging checked successfully');
            return json_encode($data);




    }





}
