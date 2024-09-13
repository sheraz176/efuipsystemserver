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
        ['Haleema', 'Sadia', 'Haleema.Sadia.abacus', 'Haleema#Sadia#abacus#2024', 'Haleema.Sadia@bpo.abacus-global.com'],
        ['Nasra', 'Farid', 'Nasra.Farid.abacus', 'Nasra#Farid#abacus#2024', 'Nasra.Farid@bpo.abacus-global.com'],
        ['Aleena', 'Shujat', 'Aleena.Shujat.abacus', 'Aleena#Shujat#abacus#2024', 'Aleena.Shujat@bpo.abacus-global.com'],
        ['Ayesha', 'Bibi', 'Ayesha.Bibi.abacus', 'Ayesha#Bibi#abacus#2024', 'Ayesha1@bpo.abacus-global.com'],
        ['Robisha', 'Safdar', 'Robisha.Safdar.abacus', 'Robisha#Safdar#abacus#2024', 'Robisha.Safdar@bpo.abacus-global.com'],
        ['Afshan', 'Siddiqui', 'Afshan.Siddiqui.abacus', 'Afshan#Siddiqui#abacus#2024', 'Afshan.Siddiqui@bpo.abacus-global.com'],
        ['Hira', 'Irfan', 'Hira.Irfan.abacus', 'Hira#Irfan#abacus#2024', 'Hira.Irfan@bpo.abacus-global.com'],
        ['Faiza', 'Aftab', 'Faiza.Aftab.abacus', 'Faiza#Aftab#abacus#2024', 'Faiza.Aftab@bpo.abacus-global.com'],
        ['Malaika', 'Waseem', 'Malaika.Waseem.abacus', 'Malaika#Waseem#abacus#2024', 'Malaika.Waseem@bpo.abacus-global.com'],
        ['Mehwish', 'Nazir', 'Mehwish.Nazir.abacus', 'Mehwish#Nazir#abacus#2024', 'Mehwish.Nazir@bpo.abacus-global.com'],
        ['Fizza', 'Faiz', 'Fizza.Faiz.abacus', 'Fizza#Faiz#abacus#2024', 'Fizza.Faiz@bpo.abacus-global.com'],
        ['Aina', 'Liaqat', 'Aina.Liaqat.abacus', 'Aina#Liaqat#abacus#2024', 'Aina.Liaqat@bpo.abacus-global.com'],
        ['Syeda', 'Manzoor', 'Syeda.Manzoor.abacus', 'Syeda#Manzoor#abacus#2024', 'Syeda.Manzoor@bpo.abacus-global.com'],
        ['Laiba', 'Amjad', 'Laiba.Amjad.abacus', 'Laiba#Amjad#abacus#2024', 'Laiba.Amjad@bpo.abacus-global.com'],
        ['Aqsa', 'Tariq', 'Aqsa.Tariq.abacus', 'Aqsa#Tariq#abacus#2024', 'Aqsa.Tariq@bpo.abacus-global.com'],
        ['Hina', 'Saleem', 'Hina.Saleem.abacus', 'Hina#Saleem#abacus#2024', 'Hina.Saleem@bpo.abacus-global.com'],
        ['Qandeel', 'Kanwal', 'Qandeel.Kanwal.abacus', 'Qandeel#Kanwal#abacus#2024', 'Qandeel.Kanwal@bpo.abacus-global.com'],
        ['Khair Ul', 'Nisa', 'KhairUl.Nisa.abacus', 'KhairUl#Nisa#abacus#2024', 'KhairUl.Nisa@bpo.abacus-global.com'],
        ['Iram', 'Shahid', 'Iram.Shahid.abacus', 'Iram#Shahid#abacus#2024', 'Iram.Shahid@bpo.abacus-global.com'],
        ['Ajwa', 'Bibi', 'Ajwa.Bibi.abacus', 'Ajwa#Bibi#abacus#2024', 'Ajwa@bpo.abacus-global.com'],
        ['Muskan', 'Butt', 'Muskan.Butt.abacus', 'Muskan#Butt#abacus#2024', 'Muskan.Butt@bpo.abacus-global.com'],
        ['Javeria', 'Usmani', 'Javeria.Usmani.abacus', 'Javeria#Usmani#abacus#2024', 'Javeria.Usmani@bpo.abacus-global.com'],
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
