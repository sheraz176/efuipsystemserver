<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription\CustomerSubscription;

class ResetCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset consecutive failure counts and update policy status';

    /**
     * Execute the console command.
     */
    public function handle()
    {

    $total = CustomerSubscription::where('policy_status', 0)
    ->where('consecutiveFailureCount', 180)
    ->count();

    dd($total);

   $this->info("Total Records Found: {$total}");

        $this->info('Starting Reset Counts Process...');

        CustomerSubscription::where('policy_status', 0)
            ->where('consecutiveFailureCount', 180)
            ->limit(20000)
            ->chunkById(500, function ($subscriptions) {

                foreach ($subscriptions as $subscription) {

                    $subscription->update([
                        'policy_status' => 1,
                        'consecutiveFailureCount' => 0,
                        'recursive_charging_date' => '2026-06-23 15:53:02',
                    ]);

                    $this->line(
                        "Updated MSISDN: {$subscription->subscriber_msisdn} | Policy ID: {$subscription->policy_id}"
                    );

                    Log::channel('sms_api')->error(
                        'Reset counts Logs update policy or recusive',
                        [
                            'Sub Id' => $subscription->subscription_id,
                            'msisdn' => $subscription->subscriber_msisdn,
                            'policy_id' => $subscription->policy_id,
                            'recursive_charging_date' => $subscription->recursive_charging_date,
                        ]
                    );
                }
            });

        $this->info('Reset Counts Process Completed Successfully.');
    }
}
