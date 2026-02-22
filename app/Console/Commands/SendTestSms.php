<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http; //
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendTestSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:message';

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
        try {
            $subscriber_msisdn = '03008758478';
            $key = 'mYjC!nc3dibleY3k';        // 16 characters
            $iv  = 'Myin!tv3ctorjCM@';        // 16 characters
            $cipher = 'AES-128-CBC';

            // Format MSISDN
            $subscriber_msisdn = ltrim($subscriber_msisdn, '+');
            if (substr($subscriber_msisdn, 0, 2) === '92') {
                // already correct
            } elseif (substr($subscriber_msisdn, 0, 1) === '0') {
                $subscriber_msisdn = '92' . substr($subscriber_msisdn, 1);
            } elseif (strlen($subscriber_msisdn) === 10) {
                $subscriber_msisdn = '92' . $subscriber_msisdn;
            }

            $smsList = [
                "Dear customer, Thank you for your trust. Rs.1 has been deducted for Family Health Insurance from your wallet. Policy T&C https://bit.ly/4gnTEWv",
                "Apni health expense claim asani se JazzCash app se submit karein ya 042-111-333-033 par call karein. Apna experience hum se zaroor share karein!"
            ];

            foreach ($smsList as $message) {
                $payload = [
                    'msisdn' => $subscriber_msisdn,
                    'content' => $message,
                    'referenceId' => uniqid(),
                ];

                // Encrypt
                $jsonData = json_encode($payload);
                $encryptedBinary = openssl_encrypt($jsonData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
                $encryptedHex = bin2hex($encryptedBinary);

                $requestBody = json_encode(['data' => $encryptedHex]);

                // cURL request
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
                    $this->error("Failed to send SMS. cURL Error: $curlError");
                    Log::channel('message_api')->error('JazzCash API cURL Error', [
                        'error' => $curlError,
                        'payload' => $payload,
                    ]);
                } else {
                    $decodedResponse = json_decode($response, true);
                    $this->info("SMS sent successfully to {$subscriber_msisdn}");
                    Log::channel('message_api')->info('Jazz SMS Sent', [
                        'msisdn' => $subscriber_msisdn,
                        'message' => $message,
                        'response' => $decodedResponse,
                    ]);
                }
            }

        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
            Log::channel('message_api')->error('Exception in recusive:onerepuee', [
                'exception' => $e->getMessage(),
            ]);
        }
    }


}
