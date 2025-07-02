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
    ['Iqra', 'Ibraheem', 'iqra.ibraheem.abacus', 'Iqra#Ibraheem##abacus#2025', 'iqra.ibraheem.abacus@gmail.com'],
    ['Zoya', 'Ilyas', 'zoya.ilyas.abacus', 'Zoya#Ilyas##abacus#2025', 'zoya.ilyas.abacus@gmail.com'],
    ['Sadia', 'Amanat', 'sadia.amanat.abacus', 'Sadia#Amanat##abacus#2025', 'sadia.amanat.abacus@gmail.com'],
    ['Kaneez', 'Fatima', 'kaneez.fatima.abacus', 'Kaneez#Fatima##abacus#2025', 'kaneez.fatima.abacus@gmail.com'],
    ['Malaika', 'Ansar', 'malaika.ansar.abacus', 'Malaika#Ansar##abacus#2025', 'malaika.ansar.abacus@gmail.com'],
    ['Saira', 'Kanwal', 'saira.kanwal.abacus', 'Saira#Kanwal##abacus#2025', 'saira.kanwal.abacus@gmail.com'],
    ['Memona', 'Amir', 'memona.amir.abacus', 'Memona#Amir##abacus#2025', 'memona.amir.abacus@gmail.com'],
    ['Alishba', 'Akbar', 'alishba.akbar.abacus', 'Alishba#Akbar##abacus#2025', 'alishba.akbar.abacus@gmail.com'],
    ['Alishba', 'Liaqat', 'alishba.liaqat.abacus', 'Alishba#Liaqat##abacus#2025', 'alishba.liaqat.abacus@gmail.com'],
    ['Rabia', 'Khaliq', 'rabia.khaliq.abacus', 'Rabia#Khaliq##abacus#2025', 'rabia.khaliq.abacus@gmail.com'],
    ['Alishba', 'Raees', 'alishba.raees.abacus', 'Alishba#Raees##abacus#2025', 'alishba.raees.abacus@gmail.com'],
    ['Neha', 'Khalid', 'neha.khalid.abacus', 'Neha#Khalid##abacus#2025', 'neha.khalid.abacus@gmail.com'],
    ['Rida', 'Sajad', 'rida.sajad.abacus', 'Rida#Sajad##abacus#2025', 'rida.sajad.abacus@gmail.com'],
    ['Saima', 'Maqsood', 'saima.maqsood.abacus', 'Saima#Maqsood##abacus#2025', 'saima.maqsood.abacus@gmail.com'],
    ['Amina', 'Ansar', 'amina.ansar.abacus', 'Amina#Ansar##abacus#2025', 'amina.ansar.abacus@gmail.com'],
    ['Sana', 'Akram', 'sana.akram2.abacus', 'Sana#Akram##abacus#2025', 'sana.akram.abacus@gmail.com'],
    ['Amna', 'Fazal', 'amna.fazal.abacus', 'Amna#Fazal##abacus#2025', 'amna.fazal.abacus@gmail.com'],
    ['Maryam', 'Tasleem', 'maryam.tasleem.abacus', 'Maryam#Tasleem##abacus#2025', 'maryam.tasleem.abacus@gmail.com'],
    ['Naima', 'Saleem', 'naima.saleem.abacus', 'Naima#Saleem##abacus#2025', 'naima.saleem.abacus@gmail.com'],
    ['Mehwish', 'Nadeem', 'mehwish.nadeem.abacus', 'Mehwish#Nadeem##abacus#2025', 'mehwish.nadeem.abacus@gmail.com'],
    ['Samira', 'Sheikh', 'samira.sheikh.abacus', 'Samira#Sheikh##abacus#2025', 'samira.sheikh.abacus@gmail.com'],
    ['Sheeza', 'Kiran', 'sheeza.kiran.abacus', 'Sheeza#Kiran##abacus#2025', 'sheeza.kiran.abacus@gmail.com'],
    ['Sehar', 'Sohail Butt', 'sehar.sohail butt.abacus', 'Sehar#Sohail Butt##abacus#2025', 'sehar.sohailbutt.abacus@gmail.com'],
    ['Tayyaba', 'Shahid', 'tayyaba.shahid.abacus', 'Tayyaba#Shahid##abacus#2025', 'tayyaba.shahid.abacus@gmail.com'],
    ['Noreen', 'Barkat', 'noreen.barkat.abacus', 'Noreen#Barkat##abacus#2025', 'noreen.barkat.abacus@gmail.com'],
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
