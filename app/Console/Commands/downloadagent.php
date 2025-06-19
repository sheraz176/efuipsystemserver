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
    ['Muqadas', 'Dilawer', 'muqadas.dilawer.abacus', 'Muqadas#Dilawer##abacus#2025', 'muqadas.dilawer.abacus@gmail.com'],
    ['Akasha', 'Fatima', 'akasha.fatima.abacus', 'Akasha#Fatima##abacus#2025', 'akasha.fatima.abacus@gmail.com'],
    ['Alisha', 'Safdar', 'alisha.safdar.abacus', 'Alisha#Safdar##abacus#2025', 'alisha.safdar.abacus@gmail.com'],
    ['Mahnoor', 'Shahzadi', 'mahnoor.shahzadi.abacus', 'Mahnoor#Shahzadi##abacus#2025', 'mahnoor.shahzadi.abacus@gmail.com'],
    ['Maryam', 'Javed', 'maryam.javed.abacus', 'Maryam#Javed##abacus#2025', 'maryam.javed.abacus@gmail.com'],
    ['Hira', 'Azeem', 'hira.azeem.abacus', 'Hira#Azeem##abacus#2025', 'hira.azeem.abacus@gmail.com'],
    ['Sania', 'Khan', 'sania.khan.abacus', 'Sania#Khan##abacus#2025', 'sania.khan.abacus@gmail.com'],
    ['Maha', 'Jamal', 'maha.jamal.abacus', 'Maha#Jamal##abacus#2025', 'maha.jamal.abacus@gmail.com'],
    ['Iqra', 'Iqbal', 'iqra.iqbal.abacus', 'Iqra#Iqbal##abacus#2025', 'iqra.iqbal.abacus@gmail.com'],
    ['Rabia', 'Ashraf', 'rabia.ashraf.abacus', 'Rabia#Ashraf##abacus#2025', 'rabia.ashraf.abacus@gmail.com'],
    ['Muqadas', 'Naeem', 'muqadas.naeem.abacus', 'Muqadas#Naeem##abacus#2025', 'muqadas.naeem.abacus@gmail.com'],
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
