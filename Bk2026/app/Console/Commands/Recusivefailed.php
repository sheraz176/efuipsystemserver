<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecusiveCounts;

class Recusivefailed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recusive:failed';

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
    // Get all active daily records
    $records = RecusiveCounts::where('status', 1)->get();

    foreach ($records as $row) {

        // Skip invalid data
        if ($row->total_recursive_today < $row->success_total) {
            continue;
        }

        // Correct failed total
        $correctFailed = $row->total_recursive_today - $row->success_total;

        // Update only if mismatch
        if ($row->failed_total != $correctFailed) {
            $row->update([
                'failed_total' => $correctFailed
            ]);
        }
    }

    $this->info('RecusiveCounts failed_total corrected successfully.');

    return 0;
}

}
