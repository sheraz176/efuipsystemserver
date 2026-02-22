<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\MonthlyStat;
use Carbon\Carbon;

class UpdateMonthlyStats extends Command
{
    protected $signature = 'stats:update-monthly';
    protected $description = 'Update monthly subscription & unsubscription stats';


public function handle()
{
    $year = now()->year;

    // ============================
    // 1) Monthly Subscription + Unsubscription
    // ============================
    $stats = CustomerSubscription::selectRaw("
            MONTH(subscription_time) as month,
            SUM(CASE WHEN policy_status = 1 THEN 1 ELSE 0 END) as subscriptions,
            SUM(CASE WHEN policy_status = 0 THEN 1 ELSE 0 END) as unsubscriptions
        ")
        ->whereYear('subscription_time', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    $monthlySubUnsub = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthlySubUnsub[$m] = [
            'subscriptions'   => $stats[$m]->subscriptions ?? 0,
            'unsubscriptions' => $stats[$m]->unsubscriptions ?? 0
        ];
    }

    // ============================
    // 2) Monthly Active Subscriptions (policy_status = 1)
    // ============================
    $activeStats = CustomerSubscription::where('policy_status', 1)
        ->whereYear('subscription_time', $year)
        ->selectRaw("MONTH(subscription_time) as month, COUNT(*) as total")
        ->groupBy('month')
        ->pluck('total', 'month');

    $monthlyActive = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthlyActive[$m] = $activeStats[$m] ?? 0;
    }

    // ============================
    // SAVE FINAL JSON
    // ============================
    MonthlyStat::updateOrCreate(
        ['year' => $year],
        [
            'data' => json_encode([
                'monthly_sub_unsub' => $monthlySubUnsub,
                'monthly_active'    => $monthlyActive
            ])
        ]
    );

    $this->info('Monthly stats updated successfully!');
}

  }
