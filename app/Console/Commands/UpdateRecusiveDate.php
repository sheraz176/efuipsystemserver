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

        $subscriptions = DB::table('customer_subscriptions')
        ->where('pulse','ivr_subscription')
        ->where('api_source','IVR Subscription')
        ->where('sales_agent','-1')
        ->where('policy_status', 1)
        ->get();

        dd($subscriptions);

        foreach($subscriptions as $subscription){
          $find_sub = CustomerSubscription::find($subscription->subscription_id);
          $find_sub->sales_agent = 1;
          $find_sub->update();
        }
    //  dd($subscriptions);
        return 'success';
    }
}

