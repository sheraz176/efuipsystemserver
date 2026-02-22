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
        // Fetch records based on your given conditions
        $subscriptions = CustomerSubscription::where('pulse', 'LFDT')
            ->where('api_source', 'LFDT')
            ->where('company_id', 20)
            ->where('policy_status', 1)
            ->whereBetween('created_at', [
                '2025-09-30 00:00:00',
                '2025-10-09 00:00:00',
            ])
            ->get();

            dd($subscriptions->count());
        // Update subscription_time for each record
        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'subscription_time' => '2025-09-30 23:08:20',
            ]);

               Log::channel('sms_api')->error("date update successfully", [
                   'subscription_id' => $subscription->subscription_id,
                    'MobileNo' => $subscription->subscriber_msisdn,

                ]);
        }

        $this->info('Subscription time updated successfully for ' . $subscriptions->count() . ' records.');

        return 0;
    }
}
