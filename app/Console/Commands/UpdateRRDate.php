<?php

namespace App\Console\Commands;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateRRDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:rr';

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
        $todayEnd = Carbon::today()->endOfDay(); // 2025-12-18 23:59:59
        $nextDay  = Carbon::tomorrow()->startOfDay(); // 2025-12-19 00:00:00

        DB::table('customer_subscriptions')
            ->where('policy_status', 1)
            ->whereIn('transaction_amount', [1, 2, 10, 12, 200, 299, 163])
            ->whereBetween('created_at', [
                '2025-10-01 00:00:00',
                $todayEnd
            ])
            ->where('recursive_charging_date', '<', $todayEnd)
            ->update([
                'recursive_charging_date' => $nextDay
            ]);

        $this->info('Recursive charging date updated successfully');

        return Command::SUCCESS;
    }
}
