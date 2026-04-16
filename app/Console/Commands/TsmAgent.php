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
