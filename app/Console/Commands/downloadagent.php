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
        ['Muhammad Usman', 'Muhammad Sarwar', 'Muhammad.Usman.Waada', 'Muhammad#Usman#Waada#2025', 'usman.sarwar.waada@gmail.com'],
        ['Usama', 'Ahmed', 'Usama.Ahmed.Waada', 'Usama#Ahmed#Waada#2025', 'usama.ahmed.waada@gmail.com'],
        ['Nimra', 'Anees', 'Nimra.Anees.Waada', 'Nimra#Anees#Waada#2025', 'nimra.anis.waada@gmal.com'],
        ['Muhammad Humza', 'Junaid Jilani', 'MuhammadHumza.JunaidJilani.Waada', 'MuhammadHumza#JunaidJilani#Waada#2025', 'Humza.jilani.waada@gmail.com'],
        ['Komal', 'Riaz', 'Komal.Riaz.Waada', 'Komal#Riaz#Waada#2025', 'komal1.riaz.waada@gmail.com'],
        ['Hadiqa', 'Anees', 'Hadiqa.Anees.Waada', 'Hadiqa#Anees#Waada#2025', 'hadiqa.anis.waada@gmail.com'],
        ['Muhammad Sumair', 'Shamim Iqbal', 'MuhammadSumair.ShamimIqbal.Waada', 'MuhammadSumair#ShamimIqbal#Waada#2025', 'm.sumairkhan.waada@gmail.com'],
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
