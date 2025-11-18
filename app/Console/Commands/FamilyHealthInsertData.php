<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Facades\Validator;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\InterestedCustomers\InterestedCustomer;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\logs;
use Carbon\Carbon;
use App\Models\CheckingRequest;
use App\Models\ConsentNumber;
use Illuminate\Support\Facades\Http;
use App\Models\SMSMsisdn;

class FamilyHealthInsertData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:familyhealthdata';

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
        $filePath = storage_path('app/jazz/jazz.csv');

        if (!file_exists($filePath)) {
            Log::channel('sms_api')->warning("Jazz CSV file not found: {$filePath}");
            return;
        }

        $handle = fopen($filePath, "r");
        if (!$handle) {
            Log::channel('sms_api')->error("Unable to open Jazz CSV file.");
            return;
        }

        $planId = 4;
        $amount = 2950;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $subscriber_msisdn = preg_replace('/[^0-9]/', '', $data[0] ?? '');

            // Normalize MSISDN
            if (substr($subscriber_msisdn, 0, 2) === '92' && strlen($subscriber_msisdn) === 12) {
                $subscriber_msisdn = '0' . substr($subscriber_msisdn, 2);
            } elseif (strlen($subscriber_msisdn) === 10) {
                $subscriber_msisdn = '0' . $subscriber_msisdn;
            }

            if (!preg_match('/^0[0-9]{10}$/', $subscriber_msisdn)) {
                Log::channel('sms_api')->warning("Invalid MSISDN skipped: {$subscriber_msisdn}");
                continue;
            }

            $product = ProductModel::where('plan_id', $planId)
                ->where('fee', $amount)
                ->first();

            if (!$product) {
                Log::channel('sms_api')->error("Product not found for MSISDN: {$subscriber_msisdn}");
                continue;
            }

            // Purani active policy deactivate karna
            $existing = CustomerSubscription::where('subscriber_msisdn', $subscriber_msisdn)
                ->where('policy_status', 1)
                ->first();

            if ($existing) {
                $existing->update([
                    'policy_status' => 0
                ]);
                Log::channel('sms_api')->info("Old policy deactivated for: {$subscriber_msisdn}");
            }

            // Random IDs
            $referenceId = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT)
                . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);

            $transactionId = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT)
                . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

            $current_time = time();
            $grace_period_time = date('Y-m-d H:i:s', strtotime('+14 days', $current_time));
            $recursive_date = date('Y-m-d H:i:s', strtotime("+{$product->duration} days", $current_time));

            // New subscription create karna
            $subscription = CustomerSubscription::create([
                'customer_id' => "1",
                'payer_cnic' => "1",
                'payer_msisdn' => $amount,
                'subscriber_cnic' => "3660286244227",
                'subscriber_msisdn' => $subscriber_msisdn,
                'beneficiary_name' => "jahangirkhann",
                'beneficiary_msisdn' => $subscriber_msisdn,
                'transaction_amount' => $product->fee,
                'transaction_status' => 1,
                'referenceId' => $referenceId,
                'cps_transaction_id' => $transactionId,
                'cps_response_text' => "Service Activated Successfully",
                'product_duration' => $product->duration,
                'plan_id' => $planId,
                'productId' => $product->product_id,
                'policy_status' => 1,
                'pulse' => "LFDT",
                'api_source' => "LFDT",
                'recursive_charging_date' => $recursive_date,
                'subscription_time' => now(),
                'grace_period_time' => $grace_period_time,
                'sales_agent' => "1",
                'company_id' => "20",
                'consent' => "(DTMF),1",
            ]);

            Log::channel('sms_api')->info('Service Activated Successfully.', [
                'MobileNo' => $subscriber_msisdn,
                'plan' => "Family Health Insurance",
                'plan_id' => $planId,
                'productId' => $product->product_id,
                'amount' => $product->fee,
                'code' => "2010",
            ]);

            // ✅ Send SMS only if plan_id = 4

if ($planId == 4) {
    $plan = PlanModel::where('plan_id', $planId)->where('status', 1)->first();
    $productCheck = ProductModel::where('plan_id', $planId)
        ->where('product_id', $product->product_id)
        ->where('status', 1)->first();

    if ($plan && $productCheck) {
        // Multiple SMS list
        $smsList = [
            "Hamari insurance per aitemad ka shukria! Apki muntakhib insurance bina izafi charges reactivate ho gai hai. Aaj hi muft faida uthaein. Call 041111333033",
            "EFU Family Health Insurance deta hai Rs 7.5 lakh tak ka family hospitalization cover, C-Section pe Rs 25,000, muft doctor se online mashwara aur bohat kuch.",
            "Aapki EFU insurance deti hai phone par doctor se muft mashwara. Abhi hamare doctor se mashwara lene ke liye 042111333033 par call karein",

        ];

        // Encryption keys
        $key    = 'mYjC!nc3dibleY3k';   // 16 characters
        $iv     = 'Myin!tv3ctorjCM@';   // 16 characters
        $cipher = 'AES-128-CBC';

        foreach ($smsList as $index => $message) {
            $payload = [
                'msisdn'      => $subscriber_msisdn,
                'content'     => $message,
                'referenceId' => uniqid(),
            ];

            // JSON encode and encrypt
            $jsonData        = json_encode($payload);
            $encryptedBinary = openssl_encrypt($jsonData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $encryptedHex    = bin2hex($encryptedBinary);

            $requestBody = json_encode(['data' => $encryptedHex]);

            // Send request via cURL
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
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::channel('sms_api')->error("JazzCash SMS send error", [
                    'MobileNo' => $subscriber_msisdn,
                    'sms'      => $message,
                    'error'    => $error
                ]);
            } else {
                Log::channel('sms_api')->info("JazzCash SMS API Response", [
                    'MobileNo' => $subscriber_msisdn,
                    'sms'      => $message,
                    'response' => $response
                ]);
            }

            // optional delay (2s) taake ekdum flood na ho
            sleep(2);
        }
    }
}

        }

        fclose($handle);

        // File ko complete folder me move karna
        $newPath = storage_path('app/jazz/complete/jazz_' . now()->format('Ymd_His') . '.csv');
        rename($filePath, $newPath);

        Log::channel('sms_api')->info("Jazz CSV file processed and moved to complete: {$newPath}");
    }
}
