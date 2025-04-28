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

        foreach ($smscustomers as $smscustomer) {
            $subscriber_msisdn = $smscustomer->msisdn;

            $plan = PlanModel::where('plan_id', $smscustomer->plan_id)->where('status', 1)->first();
            $product = ProductModel::where('plan_id', $smscustomer->plan_id)
                ->where('product_id', $smscustomer->product_id)
                ->where('status', 1)->first();

            if (!$plan || !$product) {
                continue;
            }

            $fee = $product->fee;
            $plantext = $plan->plan_name;
            $plan_id = $plan->plan_id;

            if ($plan_id == 1) {
                $link = "https://bit.ly/4d0OYD6";
                $sms = "EFU Term Life deta hai aapko Rs. 10 lak tak ka life cover, Rs 10000 ka accidental hospitalization aur Rs 2000 tak ka OPD Cover.";
            } elseif ($plan_id == 4) {
                $link = "https://bit.ly/4gnTEWv";
                $sms = "EFU Family Health Insurance deta hai Rs 5 lakh tak ka family hospitalization cover, C- Section pe Rs 25000, muft doctor se online mashwara aur bohat kuch.";
            } elseif ($plan_id == 5) {
                $link = "https://bit.ly/3MGrSXG";
                $sms = "EFU Medical insurance deta hai Rs 7.5 lakh ka hospitalization cover, unlimited online doctor se mashwara aur Rs 10000 tak ka doctor ki fees, dawai aur lab test ka coverage";
            } else {
                $link = "https://bit.ly/3KagW3u";
                $sms = "EFU Medical insurance deta hai Rs 7.5 lakh ka hospitalization cover, unlimited online doctor se mashwara aur Rs 10000 tak ka doctor ki fees, dawai aur lab test ka coverage";
            }

            $url = 'https://api.efulife.com/itssr/its_sendsms';
            $headers = [
                'Channelcode' => 'ITS',
                'Authorization' => 'Bearer XXXXAAA489SMSTOKEFU',
                'Content-Type' => 'application/json',
            ];

            $payloads = [
                [
                    'MobileNo' => $subscriber_msisdn,
                    'sender' => 'EFU-LIFE',
                    'SMS' => "Dear Customer, you’ve successfully subscribed to {$plantext}. for PKR {$fee}/-.T&Cs:{$link}",
                ],
                [
                    'MobileNo' => $subscriber_msisdn,
                    'sender' => 'EFU-LIFE',
                    'SMS' => "Ab claim karna hua nihayat asan. Claim karnay k liye 042111333033 pe call kary ya apnay claim documents support@efulife.com pe email kary.",
                ],
                [
                    'MobileNo' => $subscriber_msisdn,
                    'sender' => 'EFU-LIFE',
                    'SMS' => "Apki EFU insurance deti phone pe doctor se muft mashwaray ki sahoolat. Abhi hamaray doctor se mashwara lenay k liye dial kary 042111333033",
                ],
                [
                    'MobileNo' => $subscriber_msisdn,
                    'sender' => 'EFU-LIFE',
                    'SMS' => "1.Apka Family Health Insurance 2 din mein renew hone wala hai. Baraye karam apne wallet mein kam az kam Rs 199 ki yakeeni banayein taake aap aur aapki family is service se faida utha saky",
                ],
                [
                    'MobileNo' => $subscriber_msisdn,
                    'sender' => 'EFU-LIFE',
                    'SMS' => $sms,
                ],
            ];

            foreach ($payloads as $index => $payload) {
                try {

                    // dd($payload);
                    $response = Http::withHeaders($headers)->post($url, $payload);

                    //dd($response);

                    if ($response->successful()) {
                        Log::info("SMS " . ($index + 1) . " sent successfully", ['MobileNo' => $subscriber_msisdn]);

                        if ($index == 0) {
                            try {
                                $smscustomer->status = 1;
                                $smscustomer->response = $response->body();

                                Log::info('Before Save', [
                                    'id' => $smscustomer->id,
                                    'status' => $smscustomer->status,
                                    'response' => $smscustomer->response
                                ]);

                                $smscustomer->save();

                                Log::info('SMS customer updated successfully', ['id' => $smscustomer->id]);
                            } catch (\Exception $e) {
                                Log::error('Error while updating SMS customer', [
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Exception while sending SMS " . ($index + 1), [
                        'MobileNo' => $subscriber_msisdn,
                        'Message' => $e->getMessage()
                    ]);
                }
            }
        }
    }
}
