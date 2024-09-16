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
        ['Talal', 'Qureshi', 'Talal.Qureshi.tsm', 'Talal#Qureshi#tsm#2024', 'HD1025'],
        ['Fazeej', 'Haider', 'Fazeej.Haider.tsm', 'Fazeej#Haider#tsm#2024', 'HD1026'],
        ['Muhammad', 'Safeer', 'Muhammad.Safeer.tsm', 'Muhammad#Safeer#tsm#2024', 'HD1027'],
        ['Muhammad', 'Ashaj', 'Muhammad.Ashaj.tsm', 'Muhammad#Ashaj#tsm#2024', 'HD1028'],
        ['Muhammad', 'Usman', 'Muhammad.Usman.tsm', 'Muhammad#Usman#tsm#2024', 'HD1029'],
        ['Abdul', 'Ahad', 'Abdul.Ahad.tsm', 'Abdul#Ahad#tsm#2024', 'HD1030'],
        ['Abdullah', 'Amin', 'Abdullah.Amin.tsm', 'Abdullah#Amin#tsm#2024', 'HD1031'],
        ['Zeeshan', 'Nawaz', 'Zeeshan.Nawaz.tsm', 'Zeeshan#Nawaz#tsm#2024', 'HD1032'],
        ['Alisha', 'Mehmood', 'Alisha.Mehmood.tsm', 'Alisha#Mehmood#tsm#2024', 'HD1033'],
        ['Fiza', 'Nazar', 'Fiza.Nazar.tsm', 'Fiza#Nazar#tsm#2024', 'HD1034'],
        ['Eisha', 'Iftikhar', 'Eisha.Iftikhar.tsm', 'Eisha#Iftikhar#tsm#2024', 'HD1035'],
        ['Maria', 'Aslam', 'Maria.Aslam.tsm', 'Maria#Aslam#tsm#2024', 'HD1036'],
        ['Rimsha', 'Zafar', 'Rimsha.Zafar.tsm', 'Rimsha#Zafar#tsm#2024', 'HD1037'],
        ['Fiza', 'Ali', 'Fiza.Ali.tsm', 'Fiza#Ali#tsm#2024', 'HD1038'],
        ['Ayat', 'Nadeem', 'Ayat.Nadeem.tsm', 'Ayat#Nadeem#tsm#2024', 'HD1039'],
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
