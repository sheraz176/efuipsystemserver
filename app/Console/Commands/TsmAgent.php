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
        ['Raja', 'Hammad', 'Raja.Hammad.tsm.2025', 'Raja#Hammad#tsm#2025', 'HD1238'],
        ['Imran', 'Ali', 'Imran.Ali.tsm.2025', 'Imran#Ali#tsm#2025', 'HD1239'],
        ['Aqsa', 'Kiran', 'Aqsa.Kiran.tsm.2025', 'Aqsa#Kiran#tsm#2025', 'HD1240'],
        ['Afshan', 'Kiran', 'Afshan.Kiran.tsm.2025', 'Afshan#Kiran#tsm#2025', 'HD1241'],
        ['Iqra', 'Bukhari', 'Iqra.Bukhari.tsm.2025', 'Iqra#Bukhari#tsm#2025', 'HD1242'],
        ['Saira', 'Basit', 'Saira.Basit.tsm.2025', 'Saira#Basit#tsm#2025', 'HD1243'],
        ['Rizwana', 'Sharif', 'Rizwana.Sharif.tsm.2025', 'Rizwana#Sharif#tsm#2025', 'HD1244'],
        ['Kishwar', 'Bibi', 'Kishwar.Bibi.tsm.2025', 'Kishwar#Bibi#tsm#2025', 'HD1245'],
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
