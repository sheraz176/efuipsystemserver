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
        ['Muhammad', 'Ali', 'Muhammad.Ali.tsm.2025', 'Muhammad#Ali#tsm#2025', 'HD1208'],
        ['Muhammad', 'Usama', 'Muhammad.Usama.tsm', 'Muhammad#Usama#tsm#2025', 'HD1209'],
        ['Nimra', 'Altaf', 'Nimra.Altaf.tsm', 'Nimra#Altaf#tsm#2025', 'HD1210'],
        ['Shair', 'Ali', 'Shair.Ali.tsm', 'Shair#Ali#tsm#2025', 'HD1211'],
        ['Sadia', 'Rasheed', 'Sadia.Rasheed.tsm', 'Sadia#Rasheed#tsm#2025', 'HD1212'],
        ['Asma', 'Asif', 'Asma.Asif.tsm', 'Asma#Asif#tsm#2025', 'HD1213'],
        ['Sabahat', 'Safdar', 'Sabahat.Safdar.tsm', 'Sabahat#Safdar#tsm#2025', 'HD1215'],
        ['Alishba', 'Khan', 'Alishba.Khan.tsm', 'Alishba#Khan#tsm#2025', 'HD1216'],
        ['Anum', 'Amroz', 'Anum.Amroz.tsm', 'Anum#Amroz#tsm#2025', 'HD1217'],
        ['Neha', 'Majeed', 'Neha.Majeed.tsm', 'Neha#Majeed#tsm#2025', 'HD1218'],
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
