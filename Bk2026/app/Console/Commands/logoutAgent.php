<?php

namespace App\Console\Commands;
use App\Models\TeleSalesAgent;
use Carbon\Carbon;
use Illuminate\Console\Command;

class logoutAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logout:agent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logout Agent Successfully';

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
        // $telesalesAgents = TelesalesAgent::where('company_id',11)->get();
        $telesalesAgents = TelesalesAgent::get();
            // dd($telesalesAgents);
       foreach($telesalesAgents as $telesalesAgent){
        $telesalesAgent->islogin = "0";
        $telesalesAgent->today_logout_time = now();
        $telesalesAgent->update();
       }
        return 0;
    }
}
