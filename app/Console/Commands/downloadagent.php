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

        ['Muneeb', 'Mubashir', 'Muneeb.Mubashir.sybrid', 'Muneeb#Mubashir#sybrid#2024', 'muneebfarooqui31@gmail.com'],
        ['M Kamil', 'Ali', 'Kamil.Ali.sybrid', 'Kamil#Ali#sybrid#2024', 'hassankhan502133@gmail.com'],
        ['Imtiaz', 'Ahmad', 'Imtiaz.Ahmad.sybrid', 'Imtiaz#Ahmad#sybrid#2024', 'wafaimtiaz00@gmail.com'],
        ['Anwar', 'Saeed', 'Anwar.Saeed.sybrid', 'Anwar#Saeed#sybrid#2024', 'anwar0099saeed@gmail.com'],
        ['Shan e', 'Hassan', 'ShaneHassan.sybrid', 'ShaneHassan#sybrid#2024', 'shanehassan50@gmail.com'],
        ['Sardar Ali', 'Hassan', 'SardarAliHassan.sybrid', 'SardarAliHassan#sybrid#2024', 'sardaralihassanzahid01@gmail.com'],
        ['Sardar Muhammad', 'Hassan', 'SardarMHassan.sybrid', 'SardarMHassan#sybrid#2024', 'sardarmhassan2002@gmail.com'],
        ['Abdul', 'Baseer', 'AbdulBaseer.sybrid', 'AbdulBaseer#sybrid#2024', 'abdulbaseer8881@gmail.com'],
        ['Muhammad', 'Usman', 'MuhammadUsman.sybrid', 'MuhammadUsman#sybrid#2024', 'ukdesigner007@gmail.com'],
        ['Hafsa', 'Anwar', 'Hafsa.Anwar.sybrid', 'Hafsa#Anwar#sybrid#2024', 'sybfly5@gmail.com'],
        ['Muhammad Sikandar', 'Abbasi', 'SikandarAbbasi.sybrid', 'SikandarAbbasi#sybrid#2024', 'sikandarabbasi112233@gmail.com'],
        ['Sadaf', 'Mushtaq', 'SadafMushtaq.sybrid', 'SadafMushtaq#sybrid#2024', 'itxsadafrj9@gmail.com'],
        ['Esha Bashir', 'Abbasi', 'EshaBashirAbbasi.sybrid', 'EshaBashirAbbasi#sybrid#2024', 'eshaabbasi66@gmail.com'],
        ['Anam', 'Mustafa', 'AnamMustafa.sybrid', 'AnamMustafa#sybrid#2024', 'mustafaanum88@gmail.com'],
        ['Faizan', 'Khurshid', 'FaizanKhurshid.sybrid', 'FaizanKhurshid#sybrid#2024', 'meerfaizankhursheed@gmail.com'],
        ['Malaika', 'Arif', 'MalaikaArif.sybrid', 'MalaikaArif#sybrid#2024', 'malaikaarif33333@gmail.com'],
        ['Mohsin', 'Naseer', 'MohsinNaseer.sybrid', 'MohsinNaseer#sybrid#2024', 'mohsinnaseer63@gmail.com'],
        ['Abu', 'Huraira', 'AbuHuraira.sybrid', 'AbuHuraira#sybrid#2024', 'ahahxd8932@gmail.com'],
        ['Hafiz Muhammad', 'Hashir Kiani', 'HashirKiani.sybrid', 'HashirKiani#sybrid#2024', 'hashirkiani15000@gmail.com'],
        ['Umer', 'Nasir', 'UmerNasir.sybrid', 'UmerNasir#sybrid#2024', 'umernasir008@gmail.com'],
        ['Shahzad', 'Ahmed', 'ShahzadAhmed.sybrid', 'ShahzadAhmed#sybrid#2024', 'shahzadinfo1436@gmail.com'],
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
