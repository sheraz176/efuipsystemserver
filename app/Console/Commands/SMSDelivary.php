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


class SMSDelivary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sms';

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
        $smscustomers = SMSMsisdn::where('status', "0")->get();

          //dd($smscustomers);

        $key = 'mYjC!nc3dibleY3k';         // 16 characters
        $iv = 'Myin!tv3ctorjCM@';          // 16 characters
        $cipher = 'AES-128-CBC';

        foreach ($smscustomers as $smscustomer) {

            $subscriber_msisdn = ltrim($smscustomer->msisdn, '+'); // Remove + if any
               //dd($smscustomer->msisdn);
            if (substr($subscriber_msisdn, 0, 2) === '92') {
                // Already starts with 92 - do nothing
            } elseif (substr($subscriber_msisdn, 0, 1) === '0') {
                $subscriber_msisdn = '92' . substr($subscriber_msisdn, 1);
            } elseif (strlen($subscriber_msisdn) === 10) {
                $subscriber_msisdn = '92' . $subscriber_msisdn;
            }


            $plan = PlanModel::where('plan_id', $smscustomer->plan_id)->where('status', 1)->first();
            $product = ProductModel::where('plan_id', $smscustomer->plan_id)
                ->where('product_id', $smscustomer->product_id)->first();
          //dd($plan);

            if (!$plan || !$product) continue;

            $fee        = $product->fee ?? 0;
            $plantext   = $plan->plan_name ?? '';
            $plan_id    = $plan->plan_id ?? null;
            $product_id = $product->product_id ?? null;
            $duration   = $product->duration ?? null;

          // 92 ko 03 me convert for DB match
$db_msisdn = $smscustomer->msisdn;

if (substr($db_msisdn, 0, 2) === '92') {
    $db_msisdn = '0' . substr($db_msisdn, 2);
}

//dd($db_msisdn);
$subscription = CustomerSubscription::where('subscriber_msisdn', $db_msisdn)
    ->where('plan_id', $plan_id)
    ->where('productId', $product_id)
     ->first();
           
             //dd($subscription);

            if (!$subscription) {
                // Handle no subscription found
                return;
            }
     

            $tid = $subscription->cps_transaction_id ?? '0000000000000';

   //dd($plan_id);
// Select T&C link based on plan_id
$tcLink = '';
if ($plan_id == 1) {
    $tcLink = 'https://bit.ly/4d0OYD6';
} elseif ($plan_id == 4) {
    $tcLink = 'https://bit.ly/4hUgfu8';
} elseif ($plan_id == 5) {
    $tcLink = 'https://bit.ly/3YNJOpG';
}

// Select T&C link based on plan_id
$tcLink = '';
if ($plan_id == 1) {
    $tcLink = 'https://bit.ly/4d0OYD6';
} elseif ($plan_id == 4) {
    $tcLink = 'https://bit.ly/4hUgfu8';
} elseif ($plan_id == 5) {
    $tcLink = 'https://bit.ly/4lGPhYj';
}

// Set SMS content list
$smsList = [];

// Duration-based messages
if ($duration == 365) {
    if ($plan_id == 1) {
        $smsList[] = "Shukriya! apka {$plantext} {$fee} mein activate kar diya gaya hai. T&Cs:{$tcLink}. TID:{$tid}";
        $smsList[] = "EFU Term Life deta hai aapko Rs. 10 lakh tak ka life cover, Rs 10000 ka accidental hospitalization aur Rs 2000 tak ka OPD Cover.";
    } else {
        $smsList[] = "Shukriya! apka {$plantext} {$fee} mein activate kar diya gaya hai. T&Cs:{$tcLink}. TID:{$tid}";
    }

} elseif ($duration == 30) {
          $smsList[] = "Shukriya! apka {$plantext} monthly discounted price {$fee} mein activate kar diya gaya hai. T&Cs: {$tcLink}. TID:{$tid}";
        $smsList[] = "Muaziz Saarif, yaad rahe ke aap ke muntakhib karda plan ke mutabiq Rs.{$fee} aglay mahinay se har mahene apke JazzCash wallet se deduct kiya jaye ga.";
    

} elseif ($duration == 1) {
            $smsList[] = "Shukriya! apka {$plantext} daily discounted {$fee} mein activate kar diya gaya hai. T&Cs: {$tcLink}. TID:{$tid}";
        $smsList[] = "Muaziz saarif yaad rahay apkay muntakhib karda plan k mutabiq Rs.{$fee} rozana sirf pehlay 30 din k liye lagu hai";
        $smsList[] = "Muaziz saarif yaad rahay apkay muntakhib karda plan k mutabiq 30 din baad Rs.12 rozana apkay JazzCash wallet se deduct kiye jaengay";
    
}


// Now you can loop through $smsList to send each message

// Benefial SMS based on plan_id
if ($plan_id == 1) {
    $smsList[] = "EFU Term Life deta hai aapko Rs. 10 lakh tak ka life cover, Rs 10000 ka accidental hospitalization aur Rs 2000 tak ka OPD Cover.";
} elseif ($plan_id == 4) {
    $smsList[] = "EFU Family Health Insurance deta hai Rs 7.5 lakh tak ka family hospitalization cover, C-Section pe Rs 25,000, muft doctor se online mashwara aur bohat kuch.";
} elseif ($plan_id == 5) {
    $smsList[] = "EFU Medical Insurance deta hai Rs 8.5 lakh ka hospitalization cover, unlimited online doctor se mashwara aur Rs 10000 tak ka doctor ki fees, dawai aur lab test ka coverage.";
}


// Common messages
$smsList[] = "Aapki EFU insurance deti hai phone par doctor se muft mashwara. Abhi hamare doctor se mashwara lene ke liye 111-124-444 par call karein";
$smsList[] = "Ab claim karna nihayat asaan hai! Claim ke liye 111-124-444 par call karein ya apne claim documents jazzcashclaims@efulife.com par email karein";

// Refund message if source is AutoDebit or IVR Subscription

// Now you can loop through $smsList to send each message

            foreach ($smsList as $index => $message) {
                $payload = [
                    'msisdn' => $subscriber_msisdn,
                    'content' => $message,
                    'referenceId' => uniqid(),
                ];






                $jsonData = json_encode($payload);
                $encryptedBinary = openssl_encrypt($jsonData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
                $encryptedHex = bin2hex($encryptedBinary);

                $requestBody = json_encode([
                    'data' => $encryptedHex
                ]);

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
                curl_close($ch);

                // Log or update DB
                if ($index == 0 && $response) {
                    $smscustomer->status = 1;
                    $smscustomer->response = $response;
                    $smscustomer->save();
                }
                Log::channel('message_api')->info('Jazz SMS API.', [
                    'Link' => "https://gateway.jazzcash.com.pk/jazzcash/third-party-integration/rest/api/wso2/v1/insurance/notification",
                    'msisdn' => $subscriber_msisdn,
                    'sms' => $message,
                    'response' => $response,

                ]);

                $this->info("SMS Sent to {$subscriber_msisdn}: {$message}");
                $this->line("Response: " . $response);
            }
        }
    }
}
