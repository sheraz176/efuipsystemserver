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
        ['Sadaf', 'Sohail', 'Sadaf.Sohail.Sybrid', 'Sadaf#Sohail#Sybrid#2025', 'sadafsohail396@gmail.com'],
        ['Hafsa', 'Hafeez', 'Hafsa.Hafeez.Sybrid', 'Hafsa#Hafeez#Sybrid#2025', 'hafsahafeez414@gmail.com'],
        ['Warda', 'Kashif', 'Warda.Kashif.Sybrid', 'Warda#Kashif#Sybrid#2025', 'kashifwarda07@gmail.com'],
        ['Shazia', 'Sadiq', 'Shazia.Sadiq.Sybrid', 'Shazia#Sadiq#Sybrid#2025', 'sadiqnaseeb5@gmail.com'],
        ['Hania', 'Akram', 'Hania.Akram.Sybrid', 'Hania#Akram#Sybrid#2025', 'haniaarajputthanii@gmail.com'],
        ['Eman', 'Fatima', 'Eman.Fatima.Sybrid', 'Eman#Fatima#Sybrid#2025', 'emansheikh3344@gmail.com'],
        ['Humaira', 'Saleem', 'Humaira.Saleem.Sybrid', 'Humaira#Saleem#Sybrid#2025', 'humairasaleem844@gmail.com'],
        ['Farwa', 'Kanwal', 'Farwa.Kanwal.Sybrid', 'Farwa#Kanwal#Sybrid#2025', 'zaidifarwa628@gmail.com'],
        ['Laiba', 'Zulfiqar', 'Laiba.Zulfiqar.Sybrid', 'Laiba#Zulfiqar#Sybrid#2025', 'maherawaismaherawais08@gmail.com'],
        ['Muskan', 'Tahir', 'Muskan.Tahir.Sybrid', 'Muskan#Tahir#Sybrid#2025', 'muskane342@gmail.com'],
        ['Minahil', 'Noor', 'Minahil.Noor.Sybrid', 'Minahil#Noor#Sybrid#2025', 'minahil0714@gmail.com'],
        ['Maria', 'Majeed', 'Maria.Majeed.Sybrid', 'Maria#Majeed#Sybrid#2025', 'emanmajeed010@gmail.com'],
        ['Zohran', 'Hussain', 'Zohran.Hussain.Sybrid', 'Zohran#Hussain#Sybrid#2025', 'zagzab784@gmail.com'],
        ['Maliha', 'Zulfiqar', 'Maliha.Zulfiqar.Sybrid', 'Maliha#Zulfiqar#Sybrid#2025', 'manokhan92900@gmail.com'],
        ['Sawera', 'Kamran', 'Sawera.Kamran.Sybrid', 'Sawera#Kamran#Sybrid#2025', 'sawerakamran05@gmail.com'],
        ['Nigah', 'Amjad', 'Nigah.Amjad.Sybrid', 'Nigah#Amjad#Sybrid#2025', 'aliniguu4@gmail.com'],
        ['Mahnoor', 'Aziz', 'Mahnoor.Aziz.Sybrid', 'Mahnoor#Aziz#Sybrid#2025', 'mahnooraziz879@gmail.com'],
        ['Maha', 'Noor', 'Maha.Noor.Sybrid', 'Maha#Noor#Sybrid#2025', 'mahanoor0072@gmail.com'],
        ['Tehreem', 'Taqi', 'Tehreem.Taqi.Sybrid', 'Tehreem#Taqi#Sybrid#2025', 'szada4403@gmail.com'],
        ['Eman', 'Sheraza', 'Eman.Sheraza.Sybrid', 'Eman#Sheraza#Sybrid#2025', 'syedaes098@gmail.com'],
        ['Muhammad', 'Saad', 'Muhammad.Saad.Sybrid', 'Muhammad#Saad#Sybrid#2025', 'muhammadsaad4850@gmail.com'],
        ['Danish', 'Hafeez', 'Danish.Hafeez.Sybrid', 'Danish#Hafeez#Sybrid#2025', 'danishhafeez856@gmail.com'],
        ['Asad', 'UllahShafique', 'Asad.UllahShafique.Sybrid', 'Asad#UllahShafique#Sybrid#2025', 'meharhatim5@gmail.com'],
        ['Ahmad', 'Azeem', 'Ahmad.Azeem.Sybrid', 'Ahmad#Azeem#Sybrid#2025', 'ahmadazeem1818@gmail.com'],
        ['Rizwan', 'Haseeb', 'Rizwan.Haseeb.Sybrid', 'Rizwan#Haseeb#Sybrid#2025', 'rizwanhaseebm@gmail.com'],
        ['Muhammad', 'Hasnain', 'Muhammad.Hasnain.Sybrid', 'Muhammad#Hasnain#Sybrid#2025', 'hasnainali8868689@gmail.com'],
        ['Nabeel', 'Ahmad', 'Nabeel.Ahmad.Sybrid', 'Nabeel#Ahmad#Sybrid#2025', 'innocentcute125@gmail.com'],
        ['Ali', 'Raza2', 'Ali.Raza2.Sybrid', 'Ali#Raza2#Sybrid#2025', 'aliimtiaz0010@gmail.com'],
        ['Mazhar', 'AliSaddique', 'Mazhar.AliSaddique.Sybrid', 'Mazhar#AliSaddique#Sybrid#2025', 'mazharsiddiqui2244@gmail.com'],
        ['Ahsan', 'Nawaz', 'Ahsan.Nawaz.Sybrid', 'Ahsan#Nawaz#Sybrid#2025', 'ahsannawaz8392@gmail.com'],
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
