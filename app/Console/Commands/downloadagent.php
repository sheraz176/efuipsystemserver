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
        ['Rafia', 'Nizam', 'Rafia.Nizam.Waada', 'Rafia#Nizam#Waada#2025', 'rafia.nizam.waada@gmail.com'],
        ['Talha', 'Shahbaz', 'Talha.Shahbaz.Waada', 'Talha#Shahbaz#Waada#2025', 'talha.shahbaz.waada@gmail.com'],
        ['Nimra', 'Khalid', 'Nimra.Khalid.Waada', 'Nimra#Khalid#Waada#2025', 'nimra.khalid.waada@gmail.com'],
        ['Mehak', 'Makesh', 'Mehak.Makesh.Waada', 'Mehak#Makesh#Waada#2025', 'mehak.mukesh.waada@gmail.com'],
        ['Sheeza', 'Noor', 'Sheeza.Noor.Waada', 'Sheeza#Noor#Waada#2025', 'sheeza.noor10.waada@gmail.com'],
        ['Mehreen', 'Zahid', 'Mehreen.Zahid.Waada', 'Mehreen#Zahid#Waada#2025', 'mehreen06.zahid.waada@gmail.com'],
        ['Shabana', 'Bibi', 'Shabana.Bibi.Waada', 'Shabana#Bibi#Waada#2025', 'shabana.zahoor.waada@gmail.com'],
        ['Fareeha', 'Ahmed', 'Fareeha.Ahmed.Waada', 'Fareeha#Ahmed#Waada#2025', 'fareha13.ahmed.waada@gmail.com'],
        ['Mahrukh', 'Hanif', 'Mahrukh.Hanif.Waada', 'Mahrukh#Hanif#Waada#2025', 'mahrukh.hanif.waada@gmail.com'],
        ['Asma', 'Siddique', 'Asma.Siddique.Waada', 'Asma#Siddique#Waada#2025', 'asma.siddique.waada@gmail.com'],
        ['Mohsin', 'Khan', 'Mohsin.Khan.Waada', 'Mohsin#Khan#Waada#2025', 'mohsin.khan.waada@gmail.com'],
        ['Mujahid', 'Bilal', 'Mujahid.Bilal.Waada', 'Mujahid#Bilal#Waada#2025', 'mujahid.bilal.waada@gmail.com'],
        ['Ayesha', 'Nadeem', 'Ayesha.Nadeem.Waada', 'Ayesha#Nadeem#Waada#2025', 'ayesha.nadeem01.waada@gmail.com'],
        ['Hira', 'Miraj', 'Hira.Miraj.Waada', 'Hira#Miraj#Waada#2025', 'Hira.miraj.waada@gmail.com'],
        ['Zahira', 'Khan', 'Zahira.Khan.Waada', 'Zahira#Khan#Waada#2025', 'zahira.khan.waada@gmail.com'],
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
