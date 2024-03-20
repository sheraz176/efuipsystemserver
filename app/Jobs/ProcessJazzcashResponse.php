<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;

class ProcessJazzcashResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $response;

    /**
     * Create a new job instance.
     *
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
         $this->response;
         $response = json_decode($response, true);

         if (isset($response['data'])) 
         {
             $hexEncodedData = $response['data'];

             $binaryData = hex2bin($hexEncodedData);

             // Decrypt the data using openssl_decrypt
             $decryptedData = openssl_decrypt($binaryData, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
             
            // echo $decryptedData;

             $data = json_decode($decryptedData, true);
             
             $resultCode = $data['resultCode'];
             $resultDesc = $data['resultDesc'];
             $transactionId = $data['transactionId'];
             $failedReason = $data['failedReason'];
             $amount = $data['amount'];
             $referenceId = $data['referenceId'];
             $accountNumber = $data['accountNumber'];

             FailedSubscriptionsController::saveFailedTransactionData($transactionId, $resultCode, $resultDesc, $failedReason, $amount, $referenceId,
                $accountNumber, $planID, $product_id, $agent_id);
         }

    }
}
