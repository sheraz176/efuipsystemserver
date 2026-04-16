<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnnualSMSJOB extends Command
{
    protected $signature = 'annual:sms';
    protected $description = 'Send renewal reminder SMS via JazzCash API for annual insurance plans';

    public function handle()
    {
        $today = Carbon::today();
        $todayDate = $today->format('Y-m-d');

        $this->info("SMS Job Started : ".$todayDate);

        $totalCustomers = 0;
        $totalSmsSent = 0;

        /*
        Fetch only relevant renewals (4-6 days ahead)
        */
        DB::table('customer_subscriptions')
            ->select(
                'subscriber_msisdn',
                'plan_id',
                'transaction_amount',
                'recursive_charging_date'
            )
            ->where('product_duration', 365)
            ->where('policy_status', 1)
            ->whereBetween('recursive_charging_date', [
                Carbon::today()->addDays(4)->startOfDay(),
                Carbon::today()->addDays(6)->endOfDay()
            ])
            ->orderBy('subscription_id')
            ->chunk(500, function($customers) use ($today, $todayDate, &$totalCustomers, &$totalSmsSent) {

                foreach ($customers as $customer) {
                    $totalCustomers++;

                    $renewalDate = Carbon::parse($customer->recursive_charging_date);
                    $daysBefore = $today->diffInDays($renewalDate, false);

                    // Plan info
                    $planName = '';
                    $tcLink = '';

                    if ($customer->plan_id == 1) {
                        $planName = 'Term Life Insurance';
                        $tcLink = 'https://bit.ly/4d0OYD6';
                    } elseif ($customer->plan_id == 4) {
                        $planName = 'Family Health Insurance';
                        $tcLink = 'https://bit.ly/4hUgfu8';
                    } elseif ($customer->plan_id == 5) {
                        $planName = 'Medical Insurance';
                        $tcLink = 'https://bit.ly/3YNJOpG';
                    }

                    $messages = [];

                    // SMS Journey
                    if ($daysBefore == 6) {
                        $messages[] = "Muaziz Sarif, Aap ki {$planName} is mahine renew honay wali hai. Agar aap is service ko band karwana chahtay hain to 4444 par call karein. T&Cs: {$tcLink}";
                    } elseif ($daysBefore == 5) {
                        $messages[] = "Muaziz Sarif, Yaad dehani: Aap ki {$planName} jald renew honay wali hai. Agar aap renewal se pehle service band karwana chahtay hain to 4444 par call karein. T&Cs: {$tcLink}";
                        $messages[] = "Muaziz Sarif, apni insurance renew karwane par foran Rs.500 cashback hasil karein aur har hafte brand new iPhone jeetne ka moka payein.";
                    } elseif ($daysBefore == 4) {
                        $messages[] = "Muaziz Sarif, Aap ki {$planName} {$renewalDate->format('Y-m-d')} ko Rs.{$customer->transaction_amount} ke sath renew ho jaye gi aur yeh raqam aap ke JazzCash account se deduct ho jaye gi. Agar aap service band karwana chahtay hain to 4444 par call karein. T&Cs: {$tcLink}";
                    } else {
                        continue;
                    }

                    // Format MSISDN
                    $subscriber_msisdn = ltrim($customer->subscriber_msisdn, '+');
                    if (substr($subscriber_msisdn, 0, 2) !== '92') {
                        if (substr($subscriber_msisdn, 0, 1) === '0') {
                            $subscriber_msisdn = '92' . substr($subscriber_msisdn, 1);
                        } elseif (strlen($subscriber_msisdn) === 10) {
                            $subscriber_msisdn = '92' . $subscriber_msisdn;
                        }
                    }

                    foreach ($messages as $message) {

                        // Same day duplicate stop
                        $todayExists = DB::table('annual_sms_log')
                            ->where('subscriber_msisdn', $subscriber_msisdn)
                            ->where('message', $message)  // ? exact message ke liye check
                            ->where('sent_date', $todayDate)
                            ->exists();
                        if ($todayExists) {
                            $this->warn("Already sent today: ".$subscriber_msisdn);
                            continue;
                        }

                        // Last 7 days duplicate stop
                        $last7Days = DB::table('annual_sms_log')
                            ->where('subscriber_msisdn', $subscriber_msisdn)
                            ->where('message', $message)  // ? exact message ke liye check
                            ->where('created_at', '>=', Carbon::now()->subDays(7))
                            ->exists();
                        if ($last7Days) {
                            $this->warn("SMS sent in last 7 days: ".$subscriber_msisdn);
                            continue;
                        }

                        // SEND SMS via JazzCash
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
                            $this->info("SMS SENT â†’ ".$subscriber_msisdn);
                            $totalSmsSent++;

                        } catch (\Exception $e) {
                            $apiResponse = $e->getMessage();
                            $this->error("SMS FAILED â†’ ".$subscriber_msisdn);
                        }

                        // SAVE LOG
                        DB::table('annual_sms_log')->insert([
                            'subscriber_msisdn' => $subscriber_msisdn,
                            'message' => $message,
                            'api_response' => json_encode($apiResponse),
                            'sent_date' => $todayDate,
                            'created_at' => now()
                        ]);
                    }
                }
            });

        $this->line("=======================================");
        $this->info("Total Customers Checked : ".$totalCustomers);
        $this->info("Total SMS Sent : ".$totalSmsSent);
        $this->line("=======================================");
        $this->info("SMS Job Completed");
    }
}
