<?php

namespace App\Console\Commands;
use App\Models\Subscription\CustomerSubscription;
use App\Models\RecusiveChargingData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class UpdateRecusiveDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:recusivedate';

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
          $today = Carbon::now()->toDateString();
        $subscriptions = DB::table('customer_subscriptions')
        ->select('subscription_id', DB::raw("CONCAT('92', SUBSTRING(subscriber_msisdn, -10)) AS subscriber_msisdn"), 'transaction_amount', 'consecutiveFailureCount', 'recursive_charging_date', 'product_duration', 'plan_id', 'productId')
        ->where('policy_status', 1)
        ->where('transaction_amount',4)->where('recursive_charging_date', '>=','2024-05-05')
        ->get();
        
        foreach($subscriptions as $subscription){
          $find_sub = CustomerSubscription::find($subscription->subscription_id);
          $find_sub->recursive_charging_date = $today;
          $find_sub->update();
        }
    //  dd($subscriptions);
        return 'success';
    }
}

