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
        ['Ammara', 'Abbas', 'Ammara.Abbas.abacus', 'Ammara#Abbas#abacus#2025', 'Ammara.Abbas.abacus@gmail.com'],
        ['Muqaddas', 'Fayyaz', 'Muqaddas.Fayyaz.abacus', 'Muqaddas#Fayyaz#abacus#2025', 'Muqaddas.Fayyaz.abacus@gmail.com'],
        ['Anoosha', 'Irfan', 'Anoosha.Irfan.abacus', 'Anoosha#Irfan#abacus#2025', 'Anoosha.Irfan.abacus@gmail.com'],
        ['Zunaira', 'Shahzad', 'Zunaira.Shahzad.abacus', 'Zunaira#Shahzad#abacus#2025', 'Zunaira.Shahzad.abacus@gmail.com'],
        ['Aliza', 'Yousaf', 'Aliza.Yousaf.abacus', 'Aliza#Yousaf#abacus#2025', 'Aliza.Yousaf.abacus@gmail.com'],
        ['Sumbal', 'Zahoor', 'Sumbal.Zahoor.abacus', 'Sumbal#Zahoor#abacus#2025', 'Sumbal.Zahoor.abacus@gmail.com'],
        ['Kainat', 'Loan', 'Kainat.Loan.abacus', 'Kainat#Loan#abacus#2025', 'Kainat.Loan.abacus@gmail.com'],
        ['Asma', 'Shaukat', 'Asma.Shaukat.abacus', 'Asma#Shaukat#abacus#2025', 'Asma.Shaukat.abacus@gmail.com'],
        ['Alishba', 'Latif', 'Alishba.Latif.abacus', 'Alishba#Latif#abacus#2025', 'Alishba.Latif.abacus@gmail.com'],
        ['Hina', 'Parveen', 'Hina.Parveen.abacus', 'Hina#Parveen#abacus#2025', 'Hina.Parveen.abacus@gmail.com'],
        ['Maira', 'Naik Muhammad', 'Maira.Naik.Muhammad.abacus', 'Maira#Naik#Muhammad#abacus#2025', 'Maira.Naik.Muhammad.abacus@gmail.com'],
        ['Iram', 'Akram', 'Iram.Akram.abacus', 'Iram#Akram#abacus#2025', 'Iram.Akram.abacus@gmail.com']
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
