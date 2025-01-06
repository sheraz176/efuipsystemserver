<?php

namespace App\Console\Commands;

use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Illuminate\Console\Command;

class delplans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'del:plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'del subsecription';

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
        // Fetch subscriptions with policy_status = 0 and transaction_amount = 4
        $subscriptions = DB::table('customer_subscriptions')
            ->where('policy_status', 0)
            ->where('transaction_amount', 4)
            ->get();
        //dd($subscriptions);
        // Check if there are no subscriptions to delete
        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions found for deletion.');
            return 0;
        }

        foreach ($subscriptions as $subscription) {
            // Display subscription details in the terminal
            $this->info('Deleting Subscription:');
            $this->info("Sub-Id: {$subscription->subscription_id}");
            $this->info("Del-number: {$subscription->subscriber_msisdn}");
            $this->info("Amount: {$subscription->transaction_amount}");

            // Log the subscription details being deleted
            Log::channel('unsub_number_log')->info('Del Numbers Log.', [
                'Sub-Id' => $subscription->subscription_id,
                'Del-number' => $subscription->subscriber_msisdn,
                'Amount' => $subscription->transaction_amount,
            ]);

            // Delete related records
            $find_ref = RefundedCustomer::where('subscription_id', $subscription->subscription_id)->get();
            $find_ref->each->delete();

            $find_unsub = CustomerUnSubscription::where('subscription_id', $subscription->subscription_id)->get();
            $find_unsub->each->delete();

            $find_sub = CustomerSubscription::where('subscription_id', $subscription->subscription_id)->get();
            $find_sub->each->delete();
        }

        // Final message in terminal
        $this->info('Deletion process completed.');
        Log::channel('unsub_number_log')->info('Deletion process completed.');

        return 0;
    }
}
