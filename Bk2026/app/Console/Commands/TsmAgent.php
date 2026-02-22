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
    ['Nimra', 'Stephen', 'Nimra.Stephen.tsm.2026', 'Nimra#Stephen#tsm#2026', 'HD1561'],
    ['Abdul', 'Wasi', 'Abdul.Wasi.tsm.2026', 'Abdul#Wasi#tsm#2026', 'HD1562'],
    ['Muhammad', 'Javed', 'Muhammad.Javed.tsm.2026', 'Muhammad#Javed#tsm#2026', 'HD1563'],
    ['Namra', 'Tariq', 'Namra.Tariq.tsm.2026', 'Namra#Tariq#tsm#2026', 'HD1564'],
    ['Ali', 'Haider', 'Ali.Haider.tsm.2026', 'Ali#Haider#tsm#2026', 'HD1565'],
    ['Muhammad', 'Arslan', 'Muhammad.Arslan.tsm.2026', 'Muhammad#Arslan#tsm#2026', 'HD1566'],
    ['Muhammad', 'Faizan', 'Muhammad.Faizan.tsm.2026', 'Muhammad#Faizan#tsm#2026', 'HD1567'],
    ['Muhammad', 'Saifullah', 'Muhammad.Saifullah.tsm.2026', 'Muhammad#Saifullah#tsm#2026', 'HD1568'],
    ['Muhammad Faizan', 'Akbar', 'MuhammadFaizan.Akbar.tsm.2026', 'Muhammad Faizan#Akbar#tsm#2026', 'HD1569'],
    ['Arooj', 'Shahbaz', 'Arooj.Shahbaz.tsm.2026', 'Arooj#Shahbaz#tsm#2026', 'HD1570'],
    ['Urwa', 'Allah Ditta', 'Urwa.AllahDitta.tsm.2026', 'Urwa#Allah Ditta#tsm#2026', 'HD1571'],
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
