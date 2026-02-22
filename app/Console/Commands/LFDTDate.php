<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\logs;
use Illuminate\Support\Facades\Log;

class LFDTDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lftd:date';

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
    // Fetch records based on given conditions
    $subscriptions = CustomerSubscription::where('pulse', 'LFDT')
        ->where('api_source', 'LFDT')
        ->where('company_id', 20)
        ->where('policy_status', 1)
        ->whereBetween('created_at', [
            '2025-10-01 00:00:00',
            '2025-10-09 00:00:00',
        ])
        ->get();
  dd($subscriptions->count());
    $count = $subscriptions->count();
    $this->info("Total records found: $count");

    if ($count === 0) {
        $this->info('No records found for the given conditions.');
        return 0;
    }

    $i = 1;
    foreach ($subscriptions as $subscription) {
        $subscription->update([
            'subscription_time' => '2025-09-30 23:08:20',
            'created_at' => '2025-09-30 23:08:20',
            'updated_at' => '2025-09-30 23:08:20',
        ]);

        // Log file entry
        Log::channel('sms_api')->error("Date updated successfully", [
            'subscription_id' => $subscription->subscription_id,
            'MobileNo' => $subscription->subscriber_msisdn,
        ]);

        // Terminal output
        $this->info("[$i] Updated MSISDN: {$subscription->subscriber_msisdn} | Subscription ID: {$subscription->subscription_id}");
        $i++;
    }

    $this->info("? All $count records updated successfully.");

    return 0;
}


    }
