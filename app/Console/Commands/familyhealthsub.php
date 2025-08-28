<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Http\Controllers\Subscription\FailedSubscriptionsController;
use Illuminate\Support\Facades\Validator;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\Client;
use App\Models\logs;
use Carbon\Carbon;
use App\Models\CheckingRequest;
use App\Models\ConsentNumber;
use Illuminate\Support\Facades\Http;
use App\Models\SMSMsisdn;

class familyhealthsub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'family:sub';

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
        $filePath = storage_path('familyhealth.csv');
        $completedDir = storage_path('completedata');

        if (!file_exists($filePath)) {
            $this->error("❌ CSV file not found: {$filePath}");
            Log::channel('daily')->error("FamilyHealth: CSV file not found at {$filePath}");
            return 1;
        }

        if (!file_exists($completedDir)) {
            mkdir($completedDir, 0777, true);
        }

        $planId = "4";
        $product = ProductModel::where('plan_id', $planId)->where('fee', "1950")->first();

        if (!$product) {
            $this->error("❌ Product not found for plan_id={$planId}");
            Log::channel('daily')->error("FamilyHealth: Product not found for plan_id={$planId}");
            return 1;
        }

        $fee = $product->fee;
        $duration = $product->duration;
        $productId = $product->product_id;

        if (($handle = fopen($filePath, 'r')) !== false) {
            // Header skip
            fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                [$name, $subscriber_msisdn, $cnic] = $row;

                $msisdn = preg_replace('/[^0-9]/', '', $subscriber_msisdn);

                // Format number
                if (substr($msisdn, 0, 2) === '92' && strlen($msisdn) === 12) {
                    $msisdn = '0' . substr($msisdn, 2);
                } elseif (strlen($msisdn) === 10) {
                    $msisdn = '0' . $msisdn;
                }

                // Validate
                $validator = Validator::make([
                    'subscriber_msisdn' => $msisdn,
                    'cnic' => $cnic,
                    'name' => $name
                ], [
                    'subscriber_msisdn' => ['required', 'string', 'regex:/^0[0-9]{10}$/'],
                    'cnic' => 'required',
                    'name' => 'required',
                ]);

                if ($validator->fails()) {
                    $msg = "❌ Validation failed for: {$name}, {$subscriber_msisdn}";
                    $this->error($msg);
                    Log::channel('daily')->warning("FamilyHealth: {$msg}");
                    continue;
                }

                // Check existing
                $existing = CustomerSubscription::where('subscriber_msisdn', $msisdn)
                    ->where('transaction_amount', $fee)
                    ->where('policy_status', 1)
                    ->first();

                if ($existing) {
                    $msg = "⚠️ Already subscribed: {$msisdn}";
                    $this->warn($msg);
                    Log::channel('daily')->info("FamilyHealth: {$msg}");
                    continue;
                }

                // Subscription create
                $current_time = time();
                $future_time = strtotime('+14 days', $current_time);
                $grace_period_time = date('Y-m-d H:i:s', $future_time);
                $future_time_recursive = strtotime("+{$duration} days", $current_time);
                $future_time_recursive_formatted = date('Y-m-d H:i:s', $future_time_recursive);

                $referenceId = str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT)
                    . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);

                $transactionId = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT)
                    . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

                CustomerSubscription::create([
                    'customer_id' => "1",
                    'payer_cnic' => "1",
                    'payer_msisdn' => $msisdn,
                    'subscriber_cnic' => $cnic,
                    'subscriber_msisdn' => $msisdn,
                    'beneficiary_name' => $name,
                    'beneficiary_msisdn' => $msisdn,
                    'transaction_amount' => $fee,
                    'transaction_status' => 1,
                    'referenceId' => $referenceId,
                    'cps_transaction_id' => $transactionId,
                    'cps_response_text' => "Service Activated Successfully",
                    'product_duration' => $duration,
                    'plan_id' => $planId,
                    'productId' => $productId,
                    'policy_status' => 1,
                    'pulse' => "Behbud",
                    'api_source' => "Behbud",
                    'recursive_charging_date' => $future_time_recursive_formatted,
                    'subscription_time' => now(),
                    'grace_period_time' => $grace_period_time,
                    'sales_agent' => "1",
                    'company_id' => "22",
                    'consent' => "(DTMF),1",
                ]);

                $sms = new SMSMsisdn();
                $sms->msisdn = $msisdn;
                $sms->plan_id = $planId;
                $sms->product_id = $productId;
                $sms->status = "0";
                $sms->save();

                $msg = "✅ {$msisdn} subscription created successfully ({$name})";
                $this->info($msg);
                Log::channel('daily')->info("FamilyHealth: {$msg}");
            }

            fclose($handle);
        }

        // Move file to completedata
        $newPath = $completedDir . '/familyhealth_' . date('Ymd_His') . '.csv';
        rename($filePath, $newPath);

        $msg = "CSV processing completed. File moved to: {$newPath}";
        $this->info($msg);
        Log::channel('daily')->info("FamilyHealth: {$msg}");

        return 0;
    }
}
