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
    ['Nimra', 'Stephen', 'Nimra.Stephen.tsm.2026', 'Nimra#Stephen#tsm#2026', 'HD1561'],
    ['Abdul', 'Wasi', 'Abdul.Wasi.tsm.2026', 'Abdul#Wasi#tsm#2026', 'HD1562'],
    ['Muhammad', 'Javed', 'Muhammad.Javed.tsm.2026', 'Muhammad#Javed#tsm#2026', 'HD1563'],
    ['Namra', 'Tariq', 'Namra.Tariq.tsm.2026', 'Namra#Tariq#tsm#2026', 'HD1564'],
    ['Ali', 'Haider', 'Ali.Haider.tsm.2026', 'Ali#Haider#tsm#2026', 'HD1565'],
    ['Muhammad', 'Arslan', 'Muhammad.Arslan.tsm.2026', 'Muhammad#Arslan#tsm#2026', 'HD1566'],
    ['Muhammad', 'Faizan', 'Muhammad.Faizan.tsm.2026', 'Muhammad#Faizan#tsm#2026', 'HD1567'],
    ['Muhammad', 'Saifullah', 'Muhammad.Saifullah.tsm.2026', 'Muhammad#Saifullah#tsm#2026', 'HD1568'],
    ['Muhammad Faizan', 'Akbar', 'MuhammadFaizan.Akbar.tsm.2026', 'Muhammad Faizan#Akbar#tsm#2026', 'HD1569'],
    ['Arooj', 'Shahbaz', 'Arooj.Shahbaz.tsm.2026', 'Arooj#Shahbaz#tsm#2026', 'HD1570'],
    ['Urwa', 'Allah Ditta', 'Urwa.AllahDitta.tsm.2026', 'Urwa#Allah Ditta#tsm#2026', 'HD1571'],
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
