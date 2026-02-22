<?php

namespace App\Console\Commands;
use App\Models\Subscription\CustomerSubscription;
use App\Models\RecusiveChargingData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class UpdateRecusiveDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:recusivedates';

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
        // $today = Carbon::now()->toDateString();
        //  dd($today);
         $today = "2024-05-07";
        $subscriptions = DB::table('recusive_charging_data')
        ->where('amount',4)->where('charging_date', '>=','2024-05-06')
        ->get();
        //dd($subscriptions);
        foreach($subscriptions as $subscription){
          $find_sub = RecusiveChargingData::find($subscription->id);
          $find_sub->charging_date = $today;
          $find_sub->update();
        }
    //  dd($subscriptions);
        return 'success';
    }
}
