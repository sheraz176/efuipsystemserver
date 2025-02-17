<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Response;

class downloadagent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'download:agent';

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

    $agentsData = [
        ['Muhammad', 'Ali', 'Muhammad.Ali.tsm.2025', 'Muhammad#Ali#tsm#2025', 'HD1208'],
        ['Muhammad', 'Usama', 'Muhammad.Usama.tsm', 'Muhammad#Usama#tsm#2025', 'HD1209'],
        ['Nimra', 'Altaf', 'Nimra.Altaf.tsm', 'Nimra#Altaf#tsm#2025', 'HD1210'],
        ['Shair', 'Ali', 'Shair.Ali.tsm', 'Shair#Ali#tsm#2025', 'HD1211'],
        ['Sadia', 'Rasheed', 'Sadia.Rasheed.tsm', 'Sadia#Rasheed#tsm#2025', 'HD1212'],
        ['Asma', 'Asif', 'Asma.Asif.tsm', 'Asma#Asif#tsm#2025', 'HD1213'],
        ['Sabahat', 'Safdar', 'Sabahat.Safdar.tsm', 'Sabahat#Safdar#tsm#2025', 'HD1215'],
        ['Alishba', 'Khan', 'Alishba.Khan.tsm', 'Alishba#Khan#tsm#2025', 'HD1216'],
        ['Anum', 'Amroz', 'Anum.Amroz.tsm', 'Anum#Amroz#tsm#2025', 'HD1217'],
        ['Neha', 'Majeed', 'Neha.Majeed.tsm', 'Neha#Majeed#tsm#2025', 'HD1218'],
    ];

    $headers = ['First Name', 'Last Name', 'Username', 'Password','email'];

    // CSV File Generation
    $filePath = storage_path('app/agents.csv');
    $file = fopen($filePath, 'w');
    fputcsv($file, $headers);

    foreach ($agentsData as $agent) {
        fputcsv($file, $agent);
    }

    fclose($file);

    // Output a success message
    $this->info('CSV file created successfully at ' . $filePath);

    return 0; // Exit code 0 indicates success
}

}
