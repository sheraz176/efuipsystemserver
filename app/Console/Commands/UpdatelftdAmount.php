<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\logs;
use Illuminate\Support\Facades\Log;

class UpdatelftdAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:lftdamount';

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
            ->where("plan_id", "4")
            ->where("productId", "9")
            ->where('policy_status', 1)
            ->whereBetween('created_at', [
                '2025-10-30 00:00:00',
            ])
            ->get();

            dd($subscriptions->count());
         // Update subscription_time for each record
         foreach ($subscriptions as $subscription) {
            $subscription->update([
                'transaction_amount' => '2950',
                 'plan_id' => '4',
                 'productId' => '9',
            ]);

               Log::channel('sms_api')->error("date update successfully", [
                   'subscription_id' => $subscription->subscription_id,
                    'MobileNo' => $subscription->subscriber_msisdn,
                     'Amount' => "2950",

                ]);
        }

        $this->info('Subscription amount updated successfully for ' . $subscriptions->count() . ' records.');

        return 0;
    }
}
