<?php

namespace App\Console\Commands;

use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class UpdatePolicy extends Command
{
    protected $signature = 'update:policy';

    protected $description = 'Update policy in chunks';

    public function handle()
    {

        $file = storage_path('app/NetEnrollmentReport.csv');

        if (!file_exists($file)) {
            $this->error('CSV file not found.');
            return Command::FAILURE;
        }

        $handle = fopen($file, 'r');

        // Skip header
        fgetcsv($handle);

        $records = [];

        while (($row = fgetcsv($handle, 1000, ",")) !== false) {

            $records[] = [
                'msisdn' => trim($row[0] ?? ''),
                'amount' => trim($row[2] ?? ''),
            ];
        }

        fclose($handle);

        // Collection chunks
        collect($records)->chunk(100)->each(function ($chunk, $chunkIndex) {

            $this->line("==================================");
            $this->info("Processing Chunk: " . ($chunkIndex + 1));
            $this->line("==================================");

            foreach ($chunk as $record) {

                $formattedMsisdn = '0' . preg_replace('/[^0-9]/', '', $record['msisdn']);

                $formattedAmount = str_replace(',', '', $record['amount']);

                $subscription = CustomerSubscription::where('subscriber_msisdn', $formattedMsisdn)
    ->where('transaction_amount', $formattedAmount)
    ->where('product_duration', 365)
    ->where('policy_status', 1)
    ->whereBetween('subscription_time', [
        '2025-03-01 00:00:00',
        '2025-03-31 23:59:59'
    ])
    ->first();


                if (!$subscription) {

                    $this->warn("NOT FOUND => {$formattedMsisdn}");
                    continue;
                }

                // Update policy
                $subscription->policy_status = 0;
                $subscription->save();

                // Check already unsubscribed
$alreadyUnsub = CustomerUnSubscription::where('subscription_id', $subscription->subscription_id)
    ->exists();

if ($alreadyUnsub) {
    $this->warn("ALREADY UNSUB => {$formattedMsisdn}");
    continue;
}

                // Create unsub record
                CustomerUnSubscription::create([
                    'unsubscription_datetime' => now(),
                    'medium' => 'expired',
                    'subscription_id' => $subscription->subscription_id,
                    'refunded_id' => '1',
                ]);

                $this->info("UPDATED => {$formattedMsisdn}");

                Log::channel('unsub_number_log')->info('Policy Expired.', [
                    'Sub-ID' => $subscription->subscription_id,
                    'MSISDN' => $subscription->subscriber_msisdn,
                    'transaction_amount' => $subscription->transaction_amount,
                    'policy_status' => $subscription->policy_status,
                ]);
            }

            // Optional pause
            sleep(2);
        });

        $this->info("PROCESS COMPLETED");

        return Command::SUCCESS;
    }
}
