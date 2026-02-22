<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class BankPolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:policy';

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
        DB::enableQueryLog();
   // Step 1: Fetch all refunded records matching your condition
$refundedRecords = RefundedCustomer::where('medium', 'Bank Refund')
    ->where('created_at', '>=', '2025-10-20 00:00:00')
    ->get();
//dd($refundedRecords);
if ($refundedRecords->isEmpty()) {
    echo "?? No refunded records found for 'Bank Refund' after 2025-10-20.\n";
    return;
}

echo "?? Found {$refundedRecords->count()} refunded records to process...\n";
echo "-------------------------------------------------------------\n";

foreach ($refundedRecords as $refund) {
    $subscription = CustomerSubscription::find($refund->subscription_id);

    if ($subscription) {
        // ? Step 2: Reactivate policy
        $subscription->policy_status = '1';
        $subscription->update();

        // ? Step 3: Delete refund + unsubscription entries
        $refundedDeleted = RefundedCustomer::where('subscription_id', $refund->subscription_id)->delete();
        $unsubDeleted = CustomerUnSubscription::where('subscription_id', $refund->subscription_id)->delete();

        // ? Step 4: Log the action
        Log::channel('unsub_number_log')->info('Bank Refund Reversal - Policy Reactivated.', [
            'Sub-ID' => $refund->subscription_id,
            'MSISDN' => $subscription->subscriber_msisdn,
            'transaction_amount' => $subscription->transaction_amount,
        ]);

        // ? Step 5: Print to terminal
        echo "? Policy Activated: {$subscription->subscriber_msisdn} | Sub-ID: {$subscription->subscription_id}\n";
        echo "   ? RefundedCustomer deleted: {$refundedDeleted}\n";
        echo "   ? CustomerUnSubscription deleted: {$unsubDeleted}\n";
        echo "-------------------------------------------------------------\n";
    } else {
        echo "?? Subscription not found for ID: {$refund->subscription_id}\n";
    }
}

echo "?? Process completed successfully.\n";

        //  dd($subscriptions);
        return 'success';

    }
}
