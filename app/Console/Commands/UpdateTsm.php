<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConsentNumber as Consent;

class UpdateTsm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:tsm';

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
    // Fetch consent records that match the conditions and are created before or on 2024-02-18
    $consent_numbers = Consent::where('status', 1)
        ->where('consent', '(DTMF),1')
        ->where('response', 'Insufficient balance.')
        ->where('resultCode', '2009')
        ->whereIn('company_id', [11]) // Use array for whereIn condition
        ->whereDate('created_at', '<=', '2024-02-18') // Filter by date
        ->get();

    // Iterate over subscriptions and update status to 0
    foreach ($consent_numbers as $consent_number) {
        $consent_number->update(['status' => 0]);
    }

    return 0;
}

}
