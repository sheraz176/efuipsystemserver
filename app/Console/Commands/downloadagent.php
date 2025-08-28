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
    ['Tashifa', 'Shahbaz', 'tashifa.shahbaz.abacus', 'Tashifa#Shahbaz##abacus#2025', 'tashifa.shahbaz@abacus.co'],
    ['Mehak', 'Kamran', 'mehak.kamran.abacus', 'Mehak#Kamran##abacus#2025', 'mehak.kamran@abacus.co'],
    ['Maha', 'Al Shabib', 'maha.alshabib.abacus', 'Maha#Al Shabib##abacus#2025', 'maha.alshabib@abacus.co'],
    ['Anusha', 'Qayyum', 'anusha.qayyum.abacus', 'Anusha#Qayyum##abacus#2025', 'anusha.qayyum@abacus.co'],
    ['Malaika', 'Riaz', 'malaika.riaz.abacus', 'Malaika#Riaz##abacus#2025', 'malaika.riaz@abacus.co'],
    ['Farkhanda', 'Jabeen', 'farkhanda.jabeen.abacus', 'Farkhanda#Jabeen##abacus#2025', 'farkhanda.jabeen@abacus.co'],
    ['Saleha', 'Shehzadi', 'saleha.shehzadi.abacus', 'Saleha#Shehzadi##abacus#2025', 'saleha.shehzadi@abacus.co'],
    ['Iqra', 'Dawood', 'iqra.dawood.abacus', 'Iqra#Dawood##abacus#2025', 'iqra.dawood@abacus.co'],
    ['Subeen', 'Fatima', 'subeen.fatima.abacus', 'Subeen#Fatima##abacus#2025', 'subeen.fatima@abacus.co'],
    ['Sheeza', 'Abbas', 'sheeza.abbas.abacus', 'Sheeza#Abbas##abacus#2025', 'sheeza.abbas@abacus.co'],
    ['Malaika', 'Munir', 'malaika.munir.abacus', 'Malaika#Munir##abacus#2025', 'malaika.munir@abacus.co'],
    ['Ifra', 'Ashfaq', 'ifra.ashfaq.abacus', 'Ifra#Ashfaq##abacus#2025', 'ifra.ashfaq@abacus.co'],
    ['Sidra', 'Khursheed', 'sidra.khursheed.abacus', 'Sidra#Khursheed##abacus#2025', 'sidra.khursheed@abacus.co'],
    ['Zainab', 'Imran', 'zainab.imran.abacus', 'Zainab#Imran##abacus#2025', 'zainab.imran@abacus.co'],
    ['Memoona', 'Sabir', 'memoona.sabir.abacus', 'Memoona#Sabir##abacus#2025', 'memoona.sabir@abacus.co'],
    ['Isha', 'Athar Shah', 'isha.atharshah.abacus', 'Isha#Athar Shah##abacus#2025', 'isha.atharshah@abacus.co'],
    ['Taniya', 'Qadeer Ahmad', 'taniya.qadeerahmad.abacus', 'Taniya#Qadeer Ahmad##abacus#2025', 'taniya.qadeerahmad@abacus.co'],
    ['Areeba', 'Ilyaz', 'areeba.ilyaz.abacus', 'Areeba#Ilyaz##abacus#2025', 'areeba.ilyaz@abacus.co'],
    ['Esha', 'Ahsan', 'esha.ahsan.abacus', 'Esha#Ahsan##abacus#2025', 'esha.ahsan@abacus.co'],
    ['Rabia', 'Iqbal', 'rabia.iqbal.abacus', 'Rabia#Iqbal##abacus#2025', 'rabia.iqbal@abacus.co'],
    ['Nuzhat', 'Waseem', 'nuzhat.waseem.abacus', 'Nuzhat#Waseem##abacus#2025', 'nuzhat.waseem@abacus.co'],
    ['Ramsha', 'Khan', 'ramsha.khan.abacus', 'Ramsha#Khan##abacus#2025', 'ramsha.khan@abacus.co'],
    ['Syeda', 'Memona', 'syeda.memona.abacus', 'Syeda#Memona##abacus#2025', 'syeda.memona@abacus.co'],
    ['Rahima', 'Imran', 'rahima.imran.abacus', 'Rahima#Imran##abacus#2025', 'rahima.imran@abacus.co'],
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
