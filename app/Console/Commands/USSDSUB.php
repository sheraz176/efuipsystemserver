<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\Unsubscription\CustomerUnSubscription;

class USSDSUB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ussd:sub';

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

        $subscriptions = DB::table('customer_subscriptions')
    ->where('policy_status', 1)
    ->where('api_source', 'USSD Subscription')
    ->where('pulse', 'USSD Subscription')
    ->where('company_id', 15)
    ->get();
     dd($subscriptions);
foreach ($subscriptions as $subscription) {
    DB::table('customer_subscriptions')
        ->where('subscription_id', $subscription->subscription_id)
        ->update(['company_id' => 18]);

    Log::channel('unsub_number_log')->info('Company ID Change Log.', [
        'Sub-Id' => $subscription->subscription_id,
        'MSISDN-number' => $subscription->subscriber_msisdn,
        'NEW Company ID' => $subscription->company_id,
        'OLD Company ID' => 15,
    ]);
}




    }
}
