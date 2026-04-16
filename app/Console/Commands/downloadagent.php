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
    ['Natasha', 'Ashraf', 'Natasha.Ashraf.tsm.2026', 'Natasha#Ashraf#tsm#2026', 'HD1616'],
    ['Aleesha', 'Zia', 'Aleesha.Zia.tsm.2026', 'Aleesha#Zia#tsm#2026', 'HD1617'],
    ['Hammad', 'Ali', 'Hammad.Ali.tsm.2026', 'Hammad#Ali#tsm#2026', 'HD1618'],
    ['Shafqat Ullah', 'Khan', 'ShafqatUllah.Khan.tsm.2026', 'Shafqat Ullah#Khan#tsm#2026', 'HD1619'],
    ['Laaraib', 'Imran', 'Laaraib.Imran.tsm.2026', 'Laaraib#Imran#tsm#2026', 'HD1620'],
    ['Hadia', 'Nadeem', 'Hadia.Nadeem.tsm.2026', 'Hadia#Nadeem#tsm#2026', 'HD1621'],
    ['Rimsha', 'Hafeez', 'Rimsha.Hafeez.tsm.2026', 'Rimsha#Hafeez#tsm#2026', 'HD1622'],
    ['Saher', 'Safarish', 'Saher.Safarish.tsm.2026', 'Saher#Safarish#tsm#2026', 'HD1623'],
    ['Muhammad Zulfaqar', 'Khan', 'MuhammadZulfaqar.Khan.tsm.2026', 'Muhammad Zulfaqar#Khan#tsm#2026', 'HD1624'],
    ['Saif Ullah', 'Qureshi', 'SaifUllah.Qureshi.tsm.2026', 'Saif Ullah#Qureshi#tsm#2026', 'HD1625'],
    ['Arooj', 'Aslam', 'Arooj.Aslam.tsm.2026', 'Arooj#Aslam#tsm#2026', 'HD1626'],
    ['Areeb', 'Ramzan', 'Areeb.Ramzan.tsm.2026', 'Areeb#Ramzan#tsm#2026', 'HD1627'],
    ['Saqib', 'Khan', 'Saqib.Khan.tsm.2026', 'Saqib#Khan#tsm#2026', 'HD1628'],
    ['Muhammad Arif', 'Zia', 'MuhammadArif.Zia.tsm.2026', 'Muhammad Arif#Zia#tsm#2026', 'HD1629'],
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
