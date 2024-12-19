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
        ['Nosheen', 'Arzoo', 'Nosheen.Arzoo.tsm', 'Nosheen#Arzoo#tsm#2024', 'HD1141'],
        ['Hera', 'Shafaqat', 'Hera.Shafaqat.tsm', 'Hera#Shafaqat#tsm#2024', 'HD1142'],
        ['Abdullah', 'Arshad', 'Abdullah.Arshad.tsm', 'Abdullah#Arshad#tsm#2024', 'HD1143'],
        ['Tahir', 'Naeem', 'Tahir.Naeem.tsm', 'Tahir#Naeem#tsm#2024', 'HD1144'],
        ['Fatima', 'Aslam', 'Fatima.Aslam.tsm', 'Fatima#Aslam#tsm#2024', 'HD1145'],
        ['Zeenat', 'Eman', 'Zeenat.Eman.tsm', 'Zeenat#Eman#tsm#2024', 'HD1146'],
        ['Shiza', 'Dildar', 'Shiza.Dildar.tsm', 'Shiza#Dildar#tsm#2024', 'HD1147'],
        ['Neha', 'Aslam', 'Neha.Aslam.tsm', 'Neha#Aslam#tsm#2024', 'HD1148'],
        ['Zahra', 'Aftab', 'Zahra.Aftab.tsm', 'Zahra#Aftab#tsm#2024', 'HD1149'],
        ['Wajiha', 'Ali', 'Wajiha.Ali.tsm', 'Wajiha#Ali#tsm#2024', 'HD1150'],
        ['Maliha', 'Khan', 'Maliha.Khan.tsm', 'Maliha#Khan#tsm#2024', 'HD1151'],
    ];


    $headers = ['First Name', 'Last Name', 'Username', 'Password','id'];

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
