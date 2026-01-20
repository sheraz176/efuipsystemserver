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

        // Fetch fresh monthly data
        $stats = CustomerSubscription::selectRaw("
                MONTH(subscription_time) as month,
                SUM(policy_status = 1) as subscriptions,
                SUM(policy_status = 0) as unsubscriptions
            ")
            ->whereYear('subscription_time', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Create 12 month array
        $final = [];
        for ($m = 1; $m <= 12; $m++) {
            $final[$m] = [
                'subscriptions'   => $stats[$m]->subscriptions ?? 0,
                'unsubscriptions' => $stats[$m]->unsubscriptions ?? 0
            ];
        }

        MonthlyStat::updateOrCreate(
            ['year' => $year],
            ['data' => json_encode($final)]
        );

        $this->info('Monthly stats updated successfully!');
    }
}
