<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\RecusiveCharging as RecusiveChargingData;
use App\Models\Recusivefailed;
use App\Models\RecusiveCounts;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecusiveCount extends Command
{
    protected $signature = 'recusive:count';
    protected $description = 'Daily & hourly recursive charging counts';

    public function handle()
    {
        $backfillStartDate = Carbon::create(2025, 12, 4)->startOfDay();
        $saveStartDate     = Carbon::create(2026, 1, 1)->startOfDay();
        $today             = Carbon::today();
        $yesterday         = $today->copy()->subDay();

        /**
         * ===============================
         * HISTORICAL BACKFILL
         * ===============================
         */
        $date = $backfillStartDate->copy();

        while ($date->lte($yesterday)) {

            $day = $date->toDateString();

            if (!RecusiveCounts::where('date', $day)->exists()) {

                // Success counts
                $termLifeDaily = RecusiveChargingData::whereDate('created_at', $day)
                    ->where('plan_id', 1)->where('duration', 1)->count();

                $termLifeMonthly = RecusiveChargingData::whereDate('created_at', $day)
                    ->where('plan_id', 1)->where('duration', 30)->count();

                $familyHealthDaily = RecusiveChargingData::whereDate('created_at', $day)
                    ->where('plan_id', 4)->where('duration', 1)->count();

                $familyHealthMonthly = RecusiveChargingData::whereDate('created_at', $day)
                    ->where('plan_id', 4)->where('duration', 30)->count();

                $successTotal = $termLifeDaily + $termLifeMonthly + $familyHealthDaily + $familyHealthMonthly;

                // Failed
                $failedTotal = Recusivefailed::whereDate('created_at', $day)->where('looping','1st_loop')->count();
               

                // Remaining recursive (pending)
                $start = $day . ' 00:00:00';
                $end   = $day . ' 23:59:59';

                $remainingRecursive = DB::table('customer_subscriptions')
                    ->where('policy_status', 1)
                    ->whereBetween('recursive_charging_date', [$start, $end])
                    ->whereIn('transaction_amount', [1, 2, 10, 12, 200, 299, 163])
                    ->count();

                // Final total
                $runningTotal = $successTotal + $failedTotal + $remainingRecursive;

                RecusiveCounts::create([
                    'date'                        => $day,
                    'total_recursive_today'       => $runningTotal,
                    'success_total'               => $successTotal,
                    'failed_total'                => $failedTotal,
                    'term_life_daily_count'       => $termLifeDaily,
                    'term_life_monthly_count'     => $termLifeMonthly,
                    'family_health_daily_count'   => $familyHealthDaily,
                    'family_health_monthly_count' => $familyHealthMonthly,
                    'remaining_recursive'         => $remainingRecursive,
                ]);

                $this->info("? Backfilled {$day} | Total={$runningTotal}");
            }

            $date->addDay();
        }

        /**
         * ===============================
         * TODAY (HOURLY UPDATE)
         * ===============================
         */
        if ($today->gte($saveStartDate)) {

            $day = $today->toDateString();

            $termLifeDaily = RecusiveChargingData::whereDate('created_at', $today)
                ->where('plan_id', 1)->where('duration', 1)->count();

            $termLifeMonthly = RecusiveChargingData::whereDate('created_at', $today)
                ->where('plan_id', 1)->where('duration', 30)->count();

            $familyHealthDaily = RecusiveChargingData::whereDate('created_at', $today)
                ->where('plan_id', 4)->where('duration', 1)->count();

            $familyHealthMonthly = RecusiveChargingData::whereDate('created_at', $today)
                ->where('plan_id', 4)->where('duration', 30)->count();

            $successTotal = $termLifeDaily + $termLifeMonthly + $familyHealthDaily + $familyHealthMonthly;

            $failedTotal = Recusivefailed::whereDate('created_at', $today)->count();

            $start = $day . ' 00:00:00';
            $end   = $day . ' 23:59:59';

            $remainingRecursive = DB::table('customer_subscriptions')
                ->where('policy_status', 1)
                ->whereBetween('recursive_charging_date', [$start, $end])
                ->whereIn('transaction_amount', [1, 2, 10, 12, 200, 299, 163])
                ->count();

            $runningTotal = $successTotal + $failedTotal + $remainingRecursive;

            RecusiveCounts::updateOrCreate(
                ['date' => $day],
                [
                    'total_recursive_today'       => $runningTotal,
                    'success_total'               => $successTotal,
                    'failed_total'                => $failedTotal,
                    'term_life_daily_count'       => $termLifeDaily,
                    'term_life_monthly_count'     => $termLifeMonthly,
                    'family_health_daily_count'   => $familyHealthDaily,
                    'family_health_monthly_count' => $familyHealthMonthly,
                    'remaining_recursive'         => $remainingRecursive,
                ]
            );

            $this->info("?? Updated Today {$day} | Total={$runningTotal}");
        }

        return Command::SUCCESS;
    }
}