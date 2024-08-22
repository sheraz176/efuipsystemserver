<?php

namespace App\Console\Commands;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsentUpdate extends Command
{

    protected $signature = 'consent:update';


    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        // Use Eloquent to get the subscriptions
        $subscriptions = CustomerSubscription::where('policy_status', 1)
            ->where('company_id', 12)
            ->get();
        dd($subscriptions);
        // Iterate over subscriptions
        foreach ($subscriptions as $subscription) {
            $subscription->consent = "(DTMF),1";
            $subscription->save(); // Use save() instead of update() to save changes
        }

        // Return something after updating all subscriptions
        return 0;
    }


}
