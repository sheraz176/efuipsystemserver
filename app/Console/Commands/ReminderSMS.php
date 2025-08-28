<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
use App\Models\SMSMsisdn;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Subscription\CustomerSubscription;

class ReminderSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:sms';

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
    $start = Carbon::now()->addDays(3)->startOfDay();
    $end   = Carbon::now()->addDays(3)->endOfDay();

    $smscustomers = CustomerSubscription::where('product_duration', 30)
        ->where('policy_status', 1)
        ->whereBetween('recursive_charging_date', [$start, $end])
        ->get();

    $key = 'mYjC!nc3dibleY3k'; // 16 characters
    $iv = 'Myin!tv3ctorjCM@';  // 16 characters
    $cipher = 'AES-128-CBC';

    foreach ($smscustomers as $smscustomer) {
        // Format MSISDN
        $subscriber_msisdn = ltrim($smscustomer->subscriber_msisdn, '+');
        if (substr($subscriber_msisdn, 0, 2) !== '92') {
            if (substr($subscriber_msisdn, 0, 1) === '0') {
                $subscriber_msisdn = '92' . substr($subscriber_msisdn, 1);
            } elseif (strlen($subscriber_msisdn) === 10) {
                $subscriber_msisdn = '92' . $subscriber_msisdn;
            }
        }

        // Get plan & product
        $plan = PlanModel::where('plan_id', $smscustomer->plan_id)
            ->where('status', 1)
            ->first();
        $product = ProductModel::where('plan_id', $smscustomer->plan_id)
            ->where('product_id', $smscustomer->productId)
            ->first();

        if (!$plan || !$product) {
            continue; // skip this customer
        }

        $fee      = $product->fee ?? 0;
        $plantext = $plan->plan_name ?? '';
        $tid      = $smscustomer->cps_transaction_id ?? '0000000000000';

        // Build reminder SMS
        $smsList = [
            "Apka {$plantext} aglay 3 din me renew hona wala hai. Apni service active rakhnay k liye barae karam apne JazzCash wallet me kam se kam Rs. {$fee} yakeeni banae."
        ];

        // Send SMS
        foreach ($smsList as $index => $message) {
            $payload = [
                'msisdn'      => $subscriber_msisdn,
                'content'     => $message,
                'referenceId' => uniqid(),
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
            curl_close($ch);



            Log::channel('remindersms_api')->info('Jazz SMS API.', [
                'next_charging_date' => $smscustomer->recursive_charging_date,
                'msisdn'              => $subscriber_msisdn,
                'sms'                 => $message,
                'response'            => $response,
                'Link'                => "https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification",
            ]);

            $this->info("SMS Sent to {$subscriber_msisdn}: {$message}");
            $this->line("Response: " . $response);
        }
    }
}

}
