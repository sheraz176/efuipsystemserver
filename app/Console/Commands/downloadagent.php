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
    ['Muskan', 'Fatima', 'muskan.fatima.abacus', 'Muskan#Fatima##abacus#2026', 'muskan.fatima@gmail.com'],
    ['Areesha', 'Naeem', 'areesha.naeem.abacus', 'Areesha#Naeem##abacus#2026', 'areesha.naeem@gmail.com'],
    ['Javaria', 'Jamil', 'javaria.jamil.abacus', 'Javaria#Jamil##abacus#2026', 'javaria.jamil@gmail.com'],
    ['Faria', 'Ashraf', 'faria.ashraf.abacus', 'Faria#Ashraf##abacus#2026', 'faria.ashraf@gmail.com'],
    ['Tasbeeha', 'Shahzadi', 'tasbeeha.shahzadi.abacus', 'Tasbeeha#Shahzadi##abacus#2026', 'tasbeeha.shahzadi@gmail.com'],
    ['Nimra', 'Bukhari', 'nimra.bukhari.abacus', 'Nimra#Bukhari##abacus#2026', 'nimra.bukhari@gmail.com'],
    ['Maiyla', 'Mukhtar', 'maiyla.mukhtar.abacus', 'Maiyla#Mukhtar##abacus#2026', 'maiyla.mukhtar@gmail.com'],
    ['Eisha', 'Iftikhar', 'eisha.iftikhar.abacus', 'Eisha#Iftikhar##abacus#2026', 'eisha.iftikhar@gmail.com'],
    ['Alisha', 'Tasleem', 'alisha.tasleem.abacus', 'Alisha#Tasleem##abacus#2026', 'alisha.tasleem@gmail.com'],
    ['Sana', 'Faisal', 'sana.faisal.abacus', 'Sana#Faisal##abacus#2026', 'sana.faisal@gmail.com'],
    ['Syeda', 'Attiqa', 'syeda.attiqa.abacus', 'Syeda#Attiqa##abacus#2026', 'syeda.attiqa@gmail.com'],
    ['Amna', 'Noor', 'amna.noor.abacus', 'Amna#Noor##abacus#2026', 'amna.noor@gmail.com'],
    ['Muqadas', 'Ashraf', 'muqadas.ashraf.abacus', 'Muqadas#Ashraf##abacus#2026', 'muqadas.ashraf@gmail.com'],
    ['Komal', 'Shahzadi', 'komal.shahzadi.abacus', 'Komal#Shahzadi##abacus#2026', 'komal.shahzadi@gmail.com'],
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
