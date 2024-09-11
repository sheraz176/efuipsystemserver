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
        ['Areeba', 'Amir', 'Areeba.Amir.ibex', 'Areeba#Amir#ibex#2024', 'areeba.amir@ibex.co'],
        ['Irum', 'Pervaiz', 'Irum.Pervaiz.ibex', 'Irum#Pervaiz#ibex#2024', 'irum.pervaiz@ibex.co'],
        ['Laraib', 'Nafees', 'Laraib.Nafees.ibex', 'Laraib#Nafees#ibex#2024', 'laraib.nafees1@ibex.co'],
        ['Zahra', 'Batool', 'Zahra.Batool.ibex', 'Zahra#Batool#ibex#2024', 'zahra.batool1@ibex.co'],
        ['Zainab', 'Ejaz', 'Zainab.Ejaz.ibex', 'Zainab#Ejaz#ibex#2024', 'zainab.ejaz@ibex.co'],
        ['Aiman', 'Zakri', 'Aiman.Zakri.ibex', 'Aiman#Zakri#ibex#2024', 'aiman.zakri@ibex.co'],
        ['Sadia', 'Noreen', 'Sadia.Noreen.ibex', 'Sadia#Noreen#ibex#2024', 'sadia.noreen2@ibex.co'],
        ['Samreen', 'Naz', 'Samreen.Naz.ibex', 'Samreen#Naz#ibex#2024', 'samreen.naz@ibex.co'],
        ['Nadia', 'Shafique', 'Nadia.Shafique.ibex', 'Nadia#Shafique#ibex#2024', 'nadia.shafique@ibex.co'],
        ['NoureenIbex', 'Ibex', 'NoureenIbex.ibex', 'Noureen##ibex#2024', 'noureenIbex@ibex.co'],
        ['Qurat', 'ul Ain', 'Qurat.ulAin.ibex', 'Qurat#ulAin#ibex#2024', 'qurat.ain3@ibex.co'],
        ['Syeda', 'Mariam', 'Syeda.Mariam.ibex', 'Syeda#Mariam#ibex#2024', 'syeda.mariam@ibex.co'],
        ['Tayyaba', 'Riaz', 'Tayyaba.Riaz.ibex', 'Tayyaba#Riaz#ibex#2024', 'tayyaba.riaz@ibex.co'],
        ['EmanIbex', 'Ibex', 'EmanIbex.ibex', 'Eman##ibex#2024', 'emanIbex@ibex.co'],
        ['Kunzul', 'Iman', 'Kunzul.Iman.ibex', 'Kunzul#Iman#ibex#2024', 'kunzul.iman@ibex.co'],
        ['Maryam', 'Khalid', 'Maryam.Khalid.ibex', 'Maryam#Khalid#ibex#2024', 'maryam.khalid@ibex.co'],
        ['Sania', 'Abid', 'Sania.Abid.ibex', 'Sania#Abid#ibex#2024', 'sania.abid@ibex.co'],
        ['Anum', 'Adnan', 'Anum.Adnan.ibex', 'Anum#Adnan#ibex#2024', 'anum.adnan@ibex.co'],
        ['Hamiyal', 'Shah', 'Hamiyal.Shah.ibex', 'Hamiyal#Shah#ibex#2024', 'hamiyal.shah@ibex.co'],
        ['Khadija', 'Bibi', 'Khadija.Bibi.ibex', 'Khadija#Bibi#ibex#2024', 'khadija.bibi1@ibex.co'],
        ['Aliza', 'Asif', 'Aliza.Asif.ibex', 'Aliza#Asif#ibex#2024', 'aliza.qureshi@ibex.co'],
        ['Alina', 'Chouhdry', 'Alina.Chouhdry.ibex', 'Alina#Chouhdry#ibex#2024', 'alina.chouhdry@ibex.co'],
        ['Sobia', 'Aqeel', 'Sobia.Aqeel.ibex', 'Sobia#Aqeel#ibex#2024', 'sobia.aqeel@ibex.co'],
        ['Romasa', 'Feroze', 'Romasa.Feroze.ibex', 'Romasa#Feroze#ibex#2024', 'romasa.feroze@ibex.co'],
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
