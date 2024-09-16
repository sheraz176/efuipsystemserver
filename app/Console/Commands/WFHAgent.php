<?php

namespace App\Console\Commands;
use App\Models\TeleSalesAgent;
use Illuminate\Console\Command;

class WFHAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wfh:agent';

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

        $agents = DB::table('tele_sales_agents')
        ->where('company_id', 11)->whereIn('agent_id',[10,200,2000,1950])
        ->get();
        dd($agents);
        return 0;
    }
}
