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

class AnnualDedaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Annual:renewal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Annual Deduction for Insurance Plans';


    public function __construct()
    {
        parent::__construct();
    }



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
            ->where('product_duration', 365)
            ->where('policy_status', 1)
            ->get();

        dd($subscriptions->count());

        // Chunk subscriptions into 20 for parallel requests
        $chunks = $subscriptions->chunk(20);



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

                Log::channel('annual_sms_log')->info('DB LOCK: Duplicate subscription blocked.', [
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
                Log::channel('annual_sms_log')->info('Skipping duplicate deduction attempt Annual.', [
                    'sub_id' => $subscription->subscription_id,
                    'msisdn' => $subscription->subscriber_msisdn,
                    'note' => 'Already charged or failed charged today.'
                ]);

                $this->info("Skipping subscription ID {$subscription->subscription_id}, MSISDN: {$subscription->subscriber_msisdn} (already processed today)");
                continue;
            }



            // Get last successful charge datetime
            $lastCharge = RecusiveChargingModel::where('subscription_id', $subscription->subscription_id)
                ->where('duration', 365)
                ->orderByDesc('created_at')
                ->value('created_at'); // e.g. 2026-01-07 00:38:43

            // Calculate next charging date
            $nextChargingDate = $lastCharge
                ? Carbon::parse($lastCharge)->addDays(365)->toDateString() // ? sirf date
                : null;

            // Update customer_subscriptions with nextChargingDate
            DB::table('customer_subscriptions')
                ->where('subscription_id', $subscription->subscription_id)
                ->update([
                    'recursive_charging_date' => $nextChargingDate
                ]);

            // ? 365-day check (DATE ONLY)
            if ($lastCharge && Carbon::today()->lt(Carbon::parse($nextChargingDate))) {

                Log::channel('annual_sms_log')->info('Skipping duplicate deduction attempt (365-day rule).', [
                    'sub_id' => $subscription->subscription_id,
                    'msisdn' => $subscription->subscriber_msisdn,
                    'last_charge' => $lastCharge,
                    'nextcharging' => $nextChargingDate,
                    'note' => 'Already charged within last 365 days.'
                ]);

                // Save skipped info
                $skip = new SkipMsisdnModel();
                $skip->subscription_id = $subscription->subscription_id;
                $skip->msisdn = $subscription->subscriber_msisdn;
                $skip->lastcharging = $lastCharge;
                $skip->nextcharging = $nextChargingDate;
                $skip->reason = "Already charged within last 365 days";
                $skip->save();

                $this->info(
                    "Skipping subscription ID {$subscription->subscription_id},
                     MSISDN: {$subscription->subscriber_msisdn}
                         (already processed within 365 days)"
                );

                continue;
            }


            // Calculate amount
            $amount = $subscription->transaction_amount;

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
                    'recursive_charging_date' => $nextChargingDate,
                    'company_id' => '26',
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
            Log::channel('annual_sms_log')->info('Recusive Charging Api Success.', [
                'sub_id' => $subscription->subscription_id,
                'msisdn' => $subscription->subscriber_msisdn,
                'amount' => $subscription->transaction_amount,
                'plan_id' => $subscription->plan_id,
                'product_id' => $subscription->productId,
                'nextchargingdate' => $nextChargingDate,
            ]);

            Log::channel('annual_sms_log')->info('Recusive Charging Success Annual.', [
                'sub_id' => $subscription->subscription_id,
                'msisdn' => $subscription->subscriber_msisdn,
                'nextchargingdate' =>  $nextChargingDate,
                'duration' => $subscription->product_duration,
            ]);

            // Send Renewal SMS
            $this->sendRenewalSms($subscription->subscriber_msisdn, $subscription->plan_id);


            $this->info("SUCCESS: Subscription ID {$subscription->subscription_id},
            Amount: {$subscription->transaction_amount},  MSISDN: {$subscription->subscriber_msisdn}, NextChargingDate: {$nextChargingDate}");
        } else if ($data !== null) {
            DB::table('customer_subscriptions')
                ->where('subscription_id', $subscription->subscription_id)
                ->increment('consecutiveFailureCount');

            $updatedSubscription = DB::table('customer_subscriptions')
                ->where('subscription_id', $subscription->subscription_id)
                ->first();

            if ($updatedSubscription->consecutiveFailureCount >= 30) {
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

            Log::channel('annual_sms_log')->info('Recusive Charging Failed Annual.', [
                'sub_id' => $subscription->subscription_id,
                'msisdn' => $subscription->subscriber_msisdn,
                'nextchargingdate' => $nextChargingDating,
                'duration' => $subscription->product_duration,
                'amount' => $subscription->transaction_amount,
                'plan_id' => $subscription->plan_id,
                'product_id' => $subscription->productId
            ]);



            $this->info("FAILED: Subscription ID {$subscription->subscription_id}, MSISDN: {$subscription->subscriber_msisdn}
            , Amount: {$subscription->transaction_amount}, ConsecutiveFailures: {$updatedSubscription->consecutiveFailureCount}");
        }
    }

    private function sendRenewalSms($subscriber_msisdn, $plan_id)
    {
        $planName = '';
        $tcLink   = '';

        // Map plan ID to name and T&C link
        if ($plan_id == 1) {
            $planName = 'Term Life Insurance';
            $tcLink   = 'https://bit.ly/4d0OYD6';
        } elseif ($plan_id == 4) {
            $planName = 'Family Health Insurance';
            $tcLink   = 'https://bit.ly/4hUgfu8';
        } elseif ($plan_id == 5) {
            $planName = 'Medical Insurance';
            $tcLink   = 'https://bit.ly/3YNJOpG';
        }

        $message = "Muaziz Sarif, Aap ki {$planName} Insurance kamyabi se renew ho gai hai. Policy benefits jari rahain ge. T&Cs: {$tcLink}";

        // Format MSISDN
        $subscriber_msisdn = ltrim($subscriber_msisdn, '+');
        if (substr($subscriber_msisdn, 0, 2) !== '92') {
            if (substr($subscriber_msisdn, 0, 1) === '0') {
                $subscriber_msisdn = '92' . substr($subscriber_msisdn, 1);
            } elseif (strlen($subscriber_msisdn) === 10) {
                $subscriber_msisdn = '92' . $subscriber_msisdn;
            }
        }

        // Check if SMS was already sent today
        $todayDate = now()->format('Y-m-d');
        $alreadySent = DB::table('annual_sms_log')
            ->where('subscriber_msisdn', $subscriber_msisdn)
            ->where('message', $message)
            ->where('sent_date', $todayDate)
            ->exists();

        if ($alreadySent) {
            $this->warn("SMS already sent today: {$subscriber_msisdn}");
            return false;
        }

        try {
            $key = 'mYjC!nc3dibleY3k';
            $iv  = 'Myin!tv3ctorjCM@';
            $cipher = 'AES-128-CBC';

            $payload = [
                'msisdn' => $subscriber_msisdn,
                'content' => $message,
                'referenceId' => uniqid()
            ];

            $jsonData = json_encode($payload);
            $encryptedBinary = openssl_encrypt($jsonData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $encryptedHex = bin2hex($encryptedBinary);
            $requestBody = json_encode(['data' => $encryptedHex]);

            $ch = curl_init('https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-CLIENT-ID: 946658113e89d870aad2e47f715c2b72',
                'X-CLIENT-SECRET: e5a0279efbd7bd797e472d0ce9eebb69',
                'X-PARTNER-ID: 946658113e89d870aad2e47f715c2b72',
            ]);

            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                throw new \Exception("cURL Error: $curlError");
            }

            $apiResponse = json_decode($response, true);

            // Log the SMS
            DB::table('annual_sms_log')->insert([
                'subscriber_msisdn' => $subscriber_msisdn,
                'message' => $message,
                'api_response' => json_encode($apiResponse),
                'sent_date' => $todayDate,
                'created_at' => now()
            ]);

            $this->info("SMS SENT → {$subscriber_msisdn}");
            return true;
        } catch (\Exception $e) {
            $this->error("SMS FAILED → {$subscriber_msisdn}: " . $e->getMessage());
            return false;
        }
    }
}
