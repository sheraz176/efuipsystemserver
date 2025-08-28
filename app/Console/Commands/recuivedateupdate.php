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

class recuivedateupdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'date:update';

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
        // Fetch records before update
        $subscriptions = DB::table('customer_subscriptions')
            ->select(
                'subscription_id',
                DB::raw("CONCAT('92', SUBSTRING(subscriber_msisdn, -10)) AS subscriber_msisdn"),
                'transaction_amount',
                'plan_id',
                'recursive_charging_date'
            )
            ->whereDate('recursive_charging_date', "2025-08-16")
            ->where('policy_status', 1)
            ->where(function ($query) {
                $query->whereIn('transaction_amount', [2,10,200,299])
                      ->orWhere(function ($q) {
                          $q->where('transaction_amount', 1)
                            ->where('plan_id', 4);
                      });
            })
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info("No records found for update.");
            return;
        }

        // Show before update records
        $this->info("Records to be updated:");
        foreach ($subscriptions as $sub) {
            $this->line("ID: {$sub->subscription_id}, MSISDN: {$sub->subscriber_msisdn}, Date: {$sub->recursive_charging_date}");
        }

        // Update recursive_charging_date
        DB::table('customer_subscriptions')
            ->whereDate('recursive_charging_date', "2025-08-16")
            ->where('policy_status', 1)
            ->where(function ($query) {
                $query->whereIn('transaction_amount', [2,10,200,299])
                      ->orWhere(function ($q) {
                          $q->where('transaction_amount', 1)
                            ->where('plan_id', 4);
                      });
            })
            ->update([
                'recursive_charging_date' => "2025-08-18 00:00:00"
            ]);

        $this->info("Update completed! Total records updated: " . $subscriptions->count());
    }
}
