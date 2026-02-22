<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\RecusiveCharging as RecusiveChargingModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\Recusivefailed;
use App\Models\SkipMsisdnModel;
use App\Models\DailyRecursiveLock;
use Illuminate\Database\QueryException;

class MainRecusiveParallel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recusive:parallel';

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
    $today = Carbon::now()->toDateString();

    $subscriptions = DB::table('customer_subscriptions')
        ->select(
            'subscription_id',
            DB::raw("CONCAT('92', SUBSTRING(subscriber_msisdn, -10)) AS subscriber_msisdn"),
            'transaction_amount',
            'consecutiveFailureCount',
            'recursive_charging_date',
            'product_duration',
            'plan_id',
            'productId'
        )
        ->whereDate('recursive_charging_date', $today)
        ->where('policy_status', 1)
        ->whereIn('transaction_amount', [12,10,1,2,299,200,163,199])
        ->get();

   //dd($subscriptions->count());

    // Chunk subscriptions into 20 for parallel requests
    $chunks = $subscriptions->chunk(50);

      

    foreach ($chunks as $chunk) {
        $this->processChunk($chunk->toArray());
        usleep(500000); // 0.5 sec delay between chunks
    }

    $data = ['success' => true, 'message' => 'Recursive charging checked successfully'];
    return json_encode($data);
}

private function processChunk(array $subscriptions)
{
    $mh = curl_multi_init();
    $curlHandles = [];

    $key = 'mYjC!nc3dibleY3k';
    $iv = 'Myin!tv3ctorjCM@';

    foreach ($subscriptions as $subscription) {

         try {
             DailyRecursiveLock::create([
               'subscription_id' => $subscription->subscription_id,
                'process_date'    => Carbon::today()->toDateString(),
             ]);
           } catch (QueryException $e) {

               Log::channel('MainRecusive')->info('DB LOCK: Duplicate subscription blocked.', [
                  'sub_id' => $subscription->subscription_id,
                   'msisdn' => $subscription->subscriber_msisdn,
                  'date'   => Carbon::today()->toDateString(),
               ]);

         $this->info(
                 "LOCKED: Subscription {$subscription->subscription_id} already processed today"
          );

             continue; // ?? yahin loop skip
         }



       $alreadyProcessed = RecusiveChargingModel::where('subscription_id', $subscription->subscription_id)
    ->whereDate('created_at', Carbon::today())
    ->exists() 
    || Recusivefailed::where('subscription_id', $subscription->subscription_id)
    ->whereDate('created_at', Carbon::today())
    ->exists();

if ($alreadyProcessed) {
    Log::channel('MainRecusive')->info('Skipping duplicate deduction attempt.', [
        'sub_id' => $subscription->subscription_id,
        'msisdn' => $subscription->subscriber_msisdn,
        'note' => 'Already charged or failed charged today.'
    ]);

    $this->info("Skipping subscription ID {$subscription->subscription_id}, MSISDN: {$subscription->subscriber_msisdn} (already processed today)");
    continue;
}


    
        // Get last successful charge datetime
$lastCharge = RecusiveChargingModel::where('subscription_id', $subscription->subscription_id)
    ->where('duration', 30)
    ->orderByDesc('created_at')
    ->value('created_at'); // e.g. 2026-01-07 00:38:43

// Calculate next charging date
$nextChargingDate = $lastCharge
    ? Carbon::parse($lastCharge)->addDays(30)->toDateString() // ? sirf date
    : null;

// Update customer_subscriptions with nextChargingDate
DB::table('customer_subscriptions')
    ->where('subscription_id', $subscription->subscription_id)
    ->update([
        'recursive_charging_date' => $nextChargingDate
    ]);

// ? 30-day check (DATE ONLY)
if ($lastCharge && Carbon::today()->lt(Carbon::parse($nextChargingDate))) {

    Log::channel('MainRecusive')->info('Skipping duplicate deduction attempt (30-day rule).', [
        'sub_id' => $subscription->subscription_id,
        'msisdn' => $subscription->subscriber_msisdn,
        'last_charge' => $lastCharge,
        'nextcharging' => $nextChargingDate,
        'note' => 'Already charged within last 30 days.'
    ]);

    // Save skipped info
    $skip = new SkipMsisdnModel();
    $skip->subscription_id = $subscription->subscription_id;
    $skip->msisdn = $subscription->subscriber_msisdn;
    $skip->lastcharging = $lastCharge;
    $skip->nextcharging = $nextChargingDate;
    $skip->reason = "Already charged within last 30 days";
    $skip->save();

    $this->info(
        "Skipping subscription ID {$subscription->subscription_id}, 
        MSISDN: {$subscription->subscriber_msisdn} 
        (already processed within 30 days)"
    );

    continue;
}
 

        // Calculate amount
        $amount = $this->calculateAmount($subscription);

        // Prepare encrypted request data
        $requestData = $this->prepareRequestData($subscription->subscriber_msisdn, $amount);

        $ch = curl_init('https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/sub_autoPayment');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['data' => $requestData]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
            'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
            'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);

        curl_multi_add_handle($mh, $ch);
        $curlHandles[$subscription->subscription_id] = ['handle' => $ch, 'subscription' => $subscription];

      $this->info("Starting subscription ID {$subscription->subscription_id}, MSISDN: {$subscription->subscriber_msisdn}");

    }

    // Execute all cURL handles in parallel
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    // Process responses
    foreach ($curlHandles as $subId => $info) {
        $ch = $info['handle'];
        $subscription = $info['subscription'];
        $response = curl_multi_getcontent($ch);

        $this->handleResponse($subscription, $response, $key, $iv);

        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);
}

// Helper functions

private function calculateAmount($subscription)
{
    if ($subscription->transaction_amount == 2) return 299;
    if ($subscription->transaction_amount == 1) return $subscription->product_duration == 30 ? 299 : 12;
    if ($subscription->transaction_amount == 163) return 199;
    return $subscription->transaction_amount;
}

private function prepareRequestData($msisdn, $amount)
{
    $referenceId = strval(mt_rand(100000000000000000, 999999999999999999));
    $key = 'mYjC!nc3dibleY3k';
    $iv = 'Myin!tv3ctorjCM@';

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

    return bin2hex(openssl_encrypt($requestData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv));
}

private function handleResponse($subscription, $response, $key, $iv)
{
    $responseData = json_decode($response, true);
    if (!isset($responseData['data'])) return;

    $hexEncodedData = preg_replace('/[^0-9a-fA-F]/', '', $responseData['data']);
    if (strlen($hexEncodedData) % 2 !== 0) $hexEncodedData = '0' . $hexEncodedData;
    $binaryData = hex2bin($hexEncodedData);
    $data = json_decode(openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv), true);

    $nextChargingDate = Carbon::parse($subscription->recursive_charging_date)
        ->addDays($subscription->product_duration)->toDateString();

        if ($data !== null && isset($data['resultCode']) && $data['resultCode'] === "0") {

        DB::table('customer_subscriptions')
            ->where('subscription_id', $subscription->subscription_id)
            ->update([
                'recursive_charging_date' => $nextChargingDate
            ]);

        $rec = new RecusiveChargingModel();
        $rec->subscription_id = $subscription->subscription_id;
        $rec->tid = $data['transactionId'] ?? null;
        $rec->reference_id = $data['referenceId'] ?? null;
        $rec->amount = $data['amount'] ?? null;
        $rec->plan_id = $subscription->plan_id;
        $rec->product_id = $subscription->productId;
        $rec->cps_response = $data['resultDesc'] ?? $data['failedReason'] ?? null;
        $rec->charging_date = $nextChargingDate;
        $rec->customer_msisdn = $subscription->subscriber_msisdn;
        $rec->duration = $subscription->product_duration;
        $rec->save();
           Log::channel('MainRecusive')->info('Recusive Charging Api Success.', [
                        'sub_id' => $subscription->subscription_id,
                        'msisdn' => $subscription->subscriber_msisdn,
                        'nextchargingdate' => $nextChargingDate
                    ]);

          Log::channel('discount_regular_recusive')->info('Recusive Charging Success.', [
                        'sub_id' => $subscription->subscription_id,
                        'msisdn' => $subscription->subscriber_msisdn,
                        'nextchargingdate' =>  $nextChargingDate,
                         'duration' => $subscription->product_duration,
                    ]);


       $this->info("SUCCESS: Subscription ID {$subscription->subscription_id}, MSISDN: {$subscription->subscriber_msisdn}, NextChargingDate: {$nextChargingDate}");



     } else if ($data !== null) {
        DB::table('customer_subscriptions')
            ->where('subscription_id', $subscription->subscription_id)
            ->increment('consecutiveFailureCount');

        $updatedSubscription = DB::table('customer_subscriptions')
            ->where('subscription_id', $subscription->subscription_id)
            ->first();

        if ($updatedSubscription->consecutiveFailureCount >= 180) {
            DB::table('customer_subscriptions')
                ->where('subscription_id', $subscription->subscription_id)
                ->update(['policy_status' => 0]);
        }

        
$nextChargingDating = Carbon::today()->addDay(); // 2025-01-30

DB::table('customer_subscriptions')
    ->where('subscription_id', $subscription->subscription_id)
    ->update([
        'recursive_charging_date' => $nextChargingDating
    ]);


        $rec = new Recusivefailed();
        $rec->subscription_id = $subscription->subscription_id;
        $rec->tid = $data['transactionId'] ?? null;
        $rec->reference_id = $data['referenceId'] ?? null;
        $rec->amount = $data['amount'] ?? null;
        $rec->plan_id = $subscription->plan_id;
        $rec->product_id = $subscription->productId;
        $rec->cps_response = $data['resultDesc'] ?? $data['failedReason'] ?? null;
        $rec->charging_date = $nextChargingDating;
        $rec->customer_msisdn = $subscription->subscriber_msisdn;
        $rec->duration = $subscription->product_duration;
        $rec->looping = "1st_loop";
        $rec->status = "0";
        $rec->save();

 Log::channel('discount_regular_recusive')->info('Recusive Charging Failed.', [
                        'sub_id' => $subscription->subscription_id,
                        'msisdn' => $subscription->subscriber_msisdn,
                        'nextchargingdate' => $nextChargingDating,
                         'duration' => $subscription->product_duration,
                    ]);



     $this->info("FAILED: Subscription ID {$subscription->subscription_id}, MSISDN: {$subscription->subscriber_msisdn}, ConsecutiveFailures: {$updatedSubscription->consecutiveFailureCount}");

    }
}

}
