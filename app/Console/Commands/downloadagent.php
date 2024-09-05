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
        ['Sadia', 'Asghar', 'Sadia.Asghar.abacus', 'Sadia#Asghar#abacus#2024', 'Sadia@bpo.abacus-global.com'],
        ['Sana', 'Mukhtar', 'Sana.Mukhtar.abacus', 'Sana#Mukhtar#abacus#2024', 'Sana2@bpo.abacus-global.com'],
        ['Laiba', 'Khan', 'Laiba.Khan.abacus', 'Laiba#Khan#abacus#2024', 'Laiba.Khan@bpo.abacus-global.com'],
        ['Maliha', 'Nasir', 'Maliha.Nasir.abacus', 'Maliha#Nasir#abacus#2024', 'Maliha.Nasir@bpo.abacus-global.com'],
        ['Ramla', 'Nasir', 'Ramla.Nasir.abacus', 'Ramla#Nasir#abacus#2024', 'Ramla.Nasir@bpo.abacus-global.com'],
        ['Mahnoor', 'Jibran', 'Mahnoor.Jibran.abacus', 'Mahnoor#Jibran#abacus#2024', 'Mahnoor.Jibran@bpo.abacus-global.com'],
        ['Arooba', 'Irfan', 'Arooba.Irfan.abacus', 'Arooba#Irfan#abacus#2024', 'Arooba.Irfan@bpo.abacus-global.com'],
        ['Arbab', 'Arooj', 'Arbab.Arooj.abacus', 'Arbab#Arooj#abacus#2024', 'Arbab.Arooj@bpo.abacus-global.com'],
        ['Adeesha', 'Abid', 'Adeesha.Abid.abacus', 'Adeesha#Abid#abacus#2024', 'Adeesha@bpo.abacus-global.com'],
        ['Hamna', 'Ayub', 'Hamna.Ayub.abacus', 'Hamna#Ayub#abacus#2024', 'Hamna.Ayub@bpo.abacus-global.com'],
        ['Momna', 'Abid', 'Momna.Abid.abacus', 'Momna#Abid#abacus#2024', 'Momna@bpo.abacus-global.com'],
        ['Rimsha', 'Shahzad', 'Rimsha.Shahzad.abacus', 'Rimsha#Shahzad#abacus#2024', 'Rimsha.Shahzad@bpo.abacus-global.com'],
        ['Saira', 'Akbar', 'Saira.Akbar.abacus', 'Saira#Akbar#abacus#2024', 'Saira1@bpo.abacus-global.com'],
        ['Nimra', 'Gulzar', 'Nimra.Gulzar.abacus', 'Nimra#Gulzar#abacus#2024', 'Nimra2@bpo.abacus-global.com'],
        ['Janeeta', 'Azam', 'Janeeta.Azam.abacus', 'Janeeta#Azam#abacus#2024', 'Janeeta.Azam@bpo.abacus-global.com'],
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
