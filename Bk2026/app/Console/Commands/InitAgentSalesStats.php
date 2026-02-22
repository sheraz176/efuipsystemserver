<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AgentSalesStat;
use App\Models\Subscription\CustomerSubscription;


class InitAgentSalesStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'int:sale';

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
        $today = now()->toDateString();
        $month = now()->format('Y-m');
        $year  = now()->year;

        $agents = CustomerSubscription::select('sales_agent')
            ->distinct()
            ->pluck('sales_agent');

        foreach ($agents as $agentId) {

            $todayCount = CustomerSubscription::where('sales_agent', $agentId)
                ->whereDate('created_at', $today)
                ->count();

            $monthCount = CustomerSubscription::where('sales_agent', $agentId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', now()->month)
                ->count();

            $yearCount = CustomerSubscription::where('sales_agent', $agentId)
                ->whereYear('created_at', $year)
                ->count();

            AgentSalesStat::updateOrCreate(
                ['agent_id' => $agentId],
                [
                    'today_sales' => $todayCount,
                    'month_sales' => $monthCount,
                    'year_sales'  => $yearCount,
                    'stat_date'   => $today,
                    'stat_month'  => $month,
                    'stat_year'   => $year,
                ]
            );
        }

        $this->info('Agent sales stats initialized successfully');
    }
}
