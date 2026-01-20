<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeleSalesAgent;
use App\Models\Company\CompanyProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class TsmAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tsm:agent';

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
    ['Ans', 'Shahzada', 'Ans.Shahzada.tsm.2025', 'Ans#Shahzada#tsm#2025', 'HD1527'],
    ['Ad Hina', 'Javed', 'AdHina.Javed.tsm.2025', 'Ad Hina#Javed#tsm#2025', 'HD1528'],
    ['Muhammad Ahmed', 'Butt', 'MuhammadAhmed.Butt.tsm.2025', 'MuhammadAhmed#Butt#tsm#2025', 'HD1529'],
    ['Shazia', 'Mushtaq', 'Shazia.Mushtaq.tsm.2025', 'Shazia#Mushtaq#tsm#2025', 'HD1530'],
    ['Muhammad Shaban', 'Akram', 'MuhammadShaban.Akram.tsm.2025', 'MuhammadShaban#Akram#tsm#2025', 'HD1531'],
    ['Sumia', 'Rai', 'Sumia.Rai.tsm.2025', 'Sumia#Rai#tsm#2025', 'HD1532'],
    ['Aqib', 'Imtiaz', 'Aqib.Imtiaz.tsm.2025', 'Aqib#Imtiaz#tsm#2025', 'HD1533'],
    ['Misbah', 'Saleem', 'Misbah.Saleem.tsm.2025', 'Misbah#Saleem#tsm#2025', 'HD1534'],
    ['Hussnain', 'Ali', 'Hussnain.Ali.tsm.2025', 'Hussnain#Ali#tsm#2025', 'HD1535'],
    ['Hafsa', 'Umar', 'Hafsa.Umar.tsm.2025', 'Hafsa#Umar#tsm#2025', 'HD1536'],
    ['Faizan', 'Sabir', 'Faizan.Sabir.tsm.2025', 'Faizan#Sabir#tsm#2025', 'HD1537'],
    ['Seerat', 'Fatima', 'Seerat.Fatima.tsm.2025', 'Seerat#Fatima#tsm#2025', 'HD1538'],
    ['Daniyal', 'Khan', 'Daniyal.Khan.tsm.2025', 'Daniyal#Khan#tsm#2025', 'HD1539'],
    ['Maryam', 'Ghulam Farooq', 'Maryam.GhulamFarooq.tsm.2025', 'Maryam#GhulamFarooq#tsm#2025', 'HD1540'],
    ['Muhammad Abu', 'Huraira', 'MuhammadAbu.Huraira.tsm.2025', 'Muhammad Abu#Huraira#tsm#2025', 'HD1541'],
    ['Amna', 'Imtiaz', 'Amna.Imtiaz.tsm.2025', 'Amna#Imtiaz#tsm#2025', 'HD1542'],
    ['Sibgha', 'Abdul Rasheed', 'Sibgha.AbdulRasheed.tsm.2025', 'Sibgha#AbdulRasheed#tsm#2025', 'HD1543'],
    ['Muhammad', 'Rafiq', 'Muhammad.Rafiq.tsm.2025', 'Muhammad#Rafiq#tsm#2025', 'HD1544'],
    ['Muhammad', 'Mubarik', 'Muhammad.Mubarik.tsm.2025', 'Muhammad#Mubarik#tsm#2025', 'HD1545'],
    ['Hafifa', 'Arshad', 'Hafifa.Arshad.tsm.2025', 'Hafifa#Arshad#tsm#2025', 'HD1546'],
    ['Muhammad Bilal', 'Shabbir', 'MuhammadBilal.Shabbir.tsm.2025', 'Muhammad Bilal#Shabbir#tsm#2025', 'HD1547'],
    ['Oma Banin', 'Qazmi', 'OmaBanin.Qazmi.tsm.2025', 'OmaBanin#Qazmi#tsm#2025', 'HD1548'],
    ['Syeda Farwa', 'Batool Kazmi', 'SyedaFarwa.BatoolKazmi.tsm.2025', 'SyedaFarwa#Batool Kazmi#tsm#2025', 'HD1105'],
    ['Amna', 'Nadeem', 'Amna.Nadeem.tsm.2025', 'Amna#Nadeem#tsm#2025', 'HD1116'],
];






    foreach ($agentsData as $data) {
        $request = [
            'first_name' => $data[0],
            'last_name' => $data[1],
            'username' => $data[2],
            'emp_code' => $data[4],
            'status' => 1,
            'company_id' => 11,
            'password' => $data[3],
            'email' => strtolower($data[2]) . '@gmail.com',  // Using username for the email
        ];

        // Validation
        $validator = Validator::make($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:tele_sales_agents',
            'email' => 'required|email|unique:tele_sales_agents',
            'status' => 'required|in:1,0',
            'company_id' => 'required',
            'password' => 'required|min:6',
            'emp_code' => 'required',
        ]);

        if ($validator->fails()) {
            $this->error("Validation failed for: " . $request['username']);
            continue;
        }

        // Creating the telesales agent record
        TeleSalesAgent::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'status' => $request['status'],
            'company_id' => $request['company_id'],
            'password' => Hash::make($request['password']),
            'islogin' => 0,
            'call_status' => 0,
            'today_login_time' => now(),
            'today_logout_time' => now(),
            'emp_code' => $request['emp_code'],
        ]);

        $this->info("Created Telesales Agent: " . $request['username']);
    }

    return 0;
}

}
