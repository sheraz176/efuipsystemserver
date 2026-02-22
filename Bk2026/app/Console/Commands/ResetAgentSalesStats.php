<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AgentSalesStat;


class ResetAgentSalesStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent-sales:reset';

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

        AgentSalesStat::chunkById(500, function ($agents) use ($today, $month, $year) {

            foreach ($agents as $stat) {

                $update = [];

                // 🔹 Daily reset
                if ($stat->stat_date !== $today) {
                    $update['today_sales'] = 0;
                    $update['stat_date'] = $today;
                }

                // 🔹 Monthly reset
                if ($stat->stat_month !== $month) {
                    $update['month_sales'] = 0;
                    $update['stat_month'] = $month;
                }

                // 🔹 Yearly reset
                if ($stat->stat_year !== $year) {
                    $update['year_sales'] = 0;
                    $update['stat_year'] = $year;
                }

                if (!empty($update)) {
                    $stat->update($update);
                }
            }
        });

        $this->info('Agent sales stats reset completed');
    }
}
