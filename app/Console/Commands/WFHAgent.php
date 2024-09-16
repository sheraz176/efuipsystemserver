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
    // Fetch the agents based on company_id and agent_id list
    $agents = TeleSalesAgent::where('company_id', 11)
        ->whereIn('agent_id', [156,427,428,429,499,500,501,502,555,556,557,558,559,560,561])->get();

    // Update the category to 1 for each agent
    foreach ($agents as $agent) {
        $agent->category = 1;
        $agent->save(); // Save the updated agent
    }

    // Output the updated agents
    dd($agents);

    return 0;
}

}
