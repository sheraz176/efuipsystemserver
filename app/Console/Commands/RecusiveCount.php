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
use App\Models\Recusivefailed;
use App\Models\RecusiveCounts;
use App\Models\RecusiveChargingData;

class RecusiveCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recusive:count';



    public function handle()
{
    $today = Carbon::today()->toDateString();

    // Check if today's record already exists
    $existing = RecusiveCounts::where('date', $today)->first();

    // 1) Aaj ki recursive_charging_date ka total (ONLY FIRST TIME)
    if (!$existing) {
        $recursiveTotal = CustomerSubscription::where('policy_status', 1)
            ->whereDate('recursive_charging_date', $today)
            ->count();
    } else {
        // Do not update, keep old value
        $recursiveTotal = $existing->total_recursive_today;
    }

    // 2) Aaj ka success total
    $successTotal = RecusiveChargingData::where('cps_response', 'Process service request successfully.')
        ->whereDate('created_at', $today)
        ->count();

    // 3) Family Health (plan_id = 4)
    $familyHealthSuccess = RecusiveChargingData::where('cps_response', 'Process service request successfully.')
        ->where('plan_id', 4)
        ->whereDate('created_at', $today)
        ->count();

    // 4) Term Life (plan_id = 1)
    $termLifeSuccess = RecusiveChargingData::where('cps_response', 'Process service request successfully.')
        ->where('plan_id', 1)
        ->whereDate('created_at', $today)
        ->count();

    // 5) Failed Total
    $failedTotal = RecusiveFailed::whereDate('created_at', $today)->count();

    // Save/Update
    RecusiveCounts::updateOrCreate(
        ['date' => $today],
        [
            'total_recursive_today'  => $recursiveTotal, // stays same after first run
            'success_total'         => $successTotal,
            'success_family_health' => $familyHealthSuccess,
            'success_term_life'     => $termLifeSuccess,
            'failed_total'          => $failedTotal,
        ]
    );
}

}
