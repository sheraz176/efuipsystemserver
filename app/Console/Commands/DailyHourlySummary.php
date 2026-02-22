<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\HourlyTransactionSummary;

class DailyHourlySummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:hourly-summary-company';

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
    $date = Carbon::today()->toDateString(); // today

    for ($hour = 0; $hour <= 23; $hour++) {

        $start = Carbon::parse($date)->startOfDay()->addHours($hour);
        $end   = Carbon::parse($date)->startOfDay()->addHours($hour + 1);

        // CUSTOMER SUBSCRIPTIONS
        $baseQuery = DB::table('customer_subscriptions')
            ->where('policy_status', 1)
            ->whereBetween('subscription_time', [$start, $end]);

        $callCenter = (clone $baseQuery)
            ->whereIn('company_id', [1,2,11,12])
            ->selectRaw('COUNT(*) as count, SUM(transaction_amount) as amount')
            ->first();

        $ivr = (clone $baseQuery)
            ->where('company_id', 14)
            ->selectRaw('COUNT(*) as count, SUM(transaction_amount) as amount')
            ->first();

        $merchant = (clone $baseQuery)
            ->where('company_id', 17)
            ->selectRaw('COUNT(*) as count, SUM(transaction_amount) as amount')
            ->first();

        $app = (clone $baseQuery)
            ->whereIn('company_id', [15,16,18])
            ->selectRaw('COUNT(*) as count, SUM(transaction_amount) as amount')
            ->first();

        $recursive = DB::table('recusive_charging_data')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('COUNT(*) as count, SUM(amount) as amount')
            ->first();

        // INSERT OR UPDATE
        $summary = HourlyTransactionSummary::updateOrCreate(
            [
                'summary_date' => $date,
                'hour' => $hour
            ],
            [
                'call_center_count' => $callCenter->count ?? 0,
                'call_center_amount' => $callCenter->amount ?? 0,
                'ivr_count' => $ivr->count ?? 0,
                'ivr_amount' => $ivr->amount ?? 0,
                'merchant_count' => $merchant->count ?? 0,
                'merchant_amount' => $merchant->amount ?? 0,
                'app_count' => $app->count ?? 0,
                'app_amount' => $app->amount ?? 0,
                'recursive_count' => $recursive->count ?? 0,
                'recursive_amount' => $recursive->amount ?? 0,
            ]
        );

        // Show info in terminal
        $this->info("Hour {$hour}: CallCenter={$callCenter->count}, IVR={$ivr->count}, Merchant={$merchant->count}, App={$app->count}, Recursive={$recursive->count} ?");
    }

    $this->info('Hourly summary for today saved successfully.');
}




}
