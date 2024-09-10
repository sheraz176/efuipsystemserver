<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeleSalesAgent;
use App\Models\AgentCount;

class SaveAgentCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agentcounts:save';
    protected $description = 'Save agent counts to the AgentCount table every hour';


    /**
     * The console command description.
     *
     * @var string
     */


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
        $companies = [
            ['id' => '11', 'name' => 'Tsm'],
            ['id' => '1', 'name' => 'Ibex'],
            ['id' => '2', 'name' => 'Abacus'],
            ['id' => '12', 'name' => 'Sybrid'],
            ['id' => '14', 'name' => 'JazzIVR']
        ];

        foreach ($companies as $company) {
            $totalCount = TeleSalesAgent::where('company_id', $company['id'])
                ->where('status', '1')
                ->count();

            $activeCount = TeleSalesAgent::where('company_id', $company['id'])
                ->where('islogin', '1')
                ->count();

            AgentCount::updateOrCreate(
                ['company_id' => $company['id'], 'created_at' => now()->format('Y-m-d H:00:00')],
                ['count' => $activeCount]
            );
        }

        $this->info('Agent counts saved successfully.');
    }
}
