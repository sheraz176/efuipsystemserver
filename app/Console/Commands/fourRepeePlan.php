<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\RecusiveCharging as RecusiveChargingModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\Unsubscription\CustomerUnSubscription;


class fourRepeePlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unsub:four';

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
        ->where('policy_status', 1)->where('transaction_amount', 4)
        ->get();

         dd($subscriptions);

        foreach ($subscriptions as $subscription) {
            DB::table('customer_subscriptions')
                ->where('subscription_id', $subscription->subscription_id)
                ->update(['policy_status' => 0]);

                Log::channel('unsub_number_log')->info('Unsub Log.',[
                    'Sub-Id' => $subscription->subscription_id,
                    'Unsub-number' => $subscription->subscriber_msisdn,
                    'Amount' => $subscription->transaction_amount,
                    ]);

                    $CustomerUnSub= CustomerUnSubscription::create([
                        'unsubscription_datetime' => now(),
                        'medium' => "Product Changed",
                        'subscription_id' => $subscription->subscription_id,
                        'refunded_id' => "001",
                         ]);

        }

        return 0;
    }
}
