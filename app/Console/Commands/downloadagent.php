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
        ['Maria', 'Safeer Abbasi', 'Maria.Safeer.Abbasi.sybrid', 'Maria#Safeer#Abbasi#sybrid#2025', 'abbasisidra0000@gmail.com'],
        ['Alishba', 'Fazal', 'Alishba.Fazal.sybrid', 'Alishba#Fazal#sybrid#2025', 'aamiralvi30@yahoo.com'],
        ['Laiba', 'Ashraf', 'Laiba.Ashraf.sybrid', 'Laiba#Ashraf#sybrid#2025', 'laibaashraf19@gmail.com'],
        ['Muhammad', 'Daniyal', 'Muhammad.Daniyal.sybrid', 'Muhammad#Daniyal#sybrid#2025', 'muhammaddaniyalaltaf044@gmail.com'],
        ['Faizan', 'Shaheen Abbasi', 'Faizan.Shaheen.Abbasi.sybrid', 'Faizan#Shaheen#Abbasi#sybrid#2025', 'faizanabbasi65757@gmail.com'],
        ['Serosh', 'Qaiser', 'Serosh.Qaiser.sybrid', 'Serosh#Qaiser#sybrid#2025', 'sehroshqaiser@gmail.com'],
        ['Matti', 'Ur Rehman', 'Matti.Ur.Rehman.sybrid', 'Matti#Ur#Rehman#sybrid#2025', 'ababa6369@gmail.com'],
        ['Ali', 'Muhammad', 'Ali.Muhammad.sybrid', 'Ali#Muhammad#sybrid#2025', 'alimuhammad1008557@gmail.com'],
        ['Fahad', 'Kazmi', 'Fahad.Kazmi.sybrid', 'Fahad#Kazmi#sybrid#2025', 'fahadkazmi545@gmail.com'],
        ['Mirfa', 'Riaz', 'Mirfa.Riaz.sybrid', 'Mirfa#Riaz#sybrid#2025', 'mariamariaaslam51@gmail.com'],
        ['Saqib', 'Abbasi', 'Saqib.Abbasi.sybrid', 'Saqib#Abbasi#sybrid#2025', 'sa@6847@gmail.com'],
        ['Iqra', 'Javaid', 'Iqra.Javaid.sybrid', 'Iqra#Javaid#sybrid#2025', 'shykhiqra7@gmail.com']
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
