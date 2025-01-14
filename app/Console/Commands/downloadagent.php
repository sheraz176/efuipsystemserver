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
        ['Mariam', 'Jabbar', 'Mariam.Jabbar.abacus', 'Mariam#Jabbar#abacus#2025', 'Mariam.Jabbar.abacus@gmail.com'],
        ['Rimsha', 'Arif', 'Rimsha.Arif.abacus', 'Rimsha#Arif#abacus#2025', 'Rimsha.Arif.abacus@gmail.com'],
        ['Mahnoor', 'Vicky', 'Mahnoor.Vicky.abacus', 'Mahnoor#Vicky#abacus#2025', 'Mahnoor.Vicky.abacus@gmail.com'],
        ['Laiba', 'Manzoor', 'Laiba.Manzoor.abacus', 'Laiba#Manzoor#abacus#2025', 'Laiba.Manzoor.abacus@gmail.com'],
        ['Zainab', 'Anwar', 'Zainab.Anwar.abacus', 'Zainab#Anwar#abacus#2025', 'Zainab.Anwar.abacus@gmail.com'],
        ['Saba', 'Shoukat', 'Saba.Shoukat.abacus', 'Saba#Shoukat#abacus#2025', 'Saba.Shoukat.abacus@gmail.com'],
        ['Ayesha', 'Tariq', 'Ayesha.Tariq.abacus', 'Ayesha#Tariq#abacus#2025', 'Ayesha.Tariq.abacus@gmail.com'],
        ['Nagina', 'Shahzadi', 'Nagina.Shahzadi.abacus', 'Nagina#Shahzadi#abacus#2025', 'Nagina.Shahzadi.abacus@gmail.com'],
        ['Zoya', 'Murad', 'Zoya.Murad.abacus', 'Zoya#Murad#abacus#2025', 'Zoya.Murad.abacus@gmail.com'],
        ['Iqra', 'Shahzadi', 'Iqra.Shahzadi.abacus', 'Iqra#Shahzadi#abacus#2025', 'Iqra.Shahzadi.abacus@gmail.com'],
        ['Zainab', 'Ashraf', 'Zainab.Ashraf.abacus', 'Zainab#Ashraf#abacus#2025', 'Zainab.Ashraf.abacus@gmail.com'],
        ['Aqsa', 'Fatima', 'Aqsa.Fatima.abacus', 'Aqsa#Fatima#abacus#2025', 'Aqsa.Fatima.abacus@gmail.com'],
        ['Saleha', 'Naseem', 'Saleha.Naseem.abacus', 'Saleha#Naseem#abacus#2025', 'Saleha.Naseem.abacus@gmail.com'],
        ['Aiman', 'Naeem', 'Aiman.Naeem.abacus', 'Aiman#Naeem#abacus#2025', 'Aiman.Naeem.abacus@gmail.com'],
        ['Aroosa', 'Khalid', 'Aroosa.Khalid.abacus', 'Aroosa#Khalid#abacus#2025', 'Aroosa.Khalid.abacus@gmail.com'],
        ['Kaukab', 'Talib', 'Kaukab.Talib.abacus', 'Kaukab#Talib#abacus#2025', 'Kaukab.Talib.abacus@gmail.com'],
        ['Muskan', 'Kawal', 'Muskan.Kawal.abacus', 'Muskan#Kawal#abacus#2025', 'Muskan.Kawal.abacus@gmail.com'],
        ['Noor', 'Ul Huda', 'Noor.Ul.Huda.abacus', 'Noor#Ul#Huda#abacus#2025', 'Noor.Ul.Huda.abacus@gmail.com'],
        ['Zarish', 'Saleem', 'Zarish.Saleem.abacus', 'Zarish#Saleem#abacus#2025', 'Zarish.Saleem.abacus@gmail.com'],
        ['Aleeza', 'Shahzad', 'Aleeza.Shahzad.abacus', 'Aleeza#Shahzad#abacus#2025', 'Aleeza.Shahzad.abacus@gmail.com'],
        ['Sania', 'Nazir', 'Sania.Nazir.abacus', 'Sania#Nazir#abacus#2025', 'Sania.Nazir.abacus@gmail.com']
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
