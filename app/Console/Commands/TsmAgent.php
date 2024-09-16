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
        ['Talal', 'Qureshi', 'Talal.Qureshi.tsm', 'Talal#Qureshi#tsm#2024', 'HD1025'],
        ['Fazeej', 'Haider', 'Fazeej.Haider.tsm', 'Fazeej#Haider#tsm#2024', 'HD1026'],
        ['Muhammad', 'Safeer', 'Muhammad.Safeer.tsm', 'Muhammad#Safeer#tsm#2024', 'HD1027'],
        ['Muhammad', 'Ashaj', 'Muhammad.Ashaj.tsm', 'Muhammad#Ashaj#tsm#2024', 'HD1028'],
        ['Muhammad', 'Usman', 'Muhammad.Usman.tsm', 'Muhammad#Usman#tsm#2024', 'HD1029'],
        ['Abdul', 'Ahad', 'Abdul.Ahad.tsm', 'Abdul#Ahad#tsm#2024', 'HD1030'],
        ['Abdullah', 'Amin', 'Abdullah.Amin.tsm', 'Abdullah#Amin#tsm#2024', 'HD1031'],
        ['Zeeshan', 'Nawaz', 'Zeeshan.Nawaz.tsm', 'Zeeshan#Nawaz#tsm#2024', 'HD1032'],
        ['Alisha', 'Mehmood', 'Alisha.Mehmood.tsm', 'Alisha#Mehmood#tsm#2024', 'HD1033'],
        ['Fiza', 'Nazar', 'Fiza.Nazar.tsm', 'Fiza#Nazar#tsm#2024', 'HD1034'],
        ['Eisha', 'Iftikhar', 'Eisha.Iftikhar.tsm', 'Eisha#Iftikhar#tsm#2024', 'HD1035'],
        ['Maria', 'Aslam', 'Maria.Aslam.tsm', 'Maria#Aslam#tsm#2024', 'HD1036'],
        ['Rimsha', 'Zafar', 'Rimsha.Zafar.tsm', 'Rimsha#Zafar#tsm#2024', 'HD1037'],
        ['Fiza', 'Ali', 'Fiza.Ali.tsm', 'Fiza#Ali#tsm#2024', 'HD1038'],
        ['Ayat', 'Nadeem', 'Ayat.Nadeem.tsm', 'Ayat#Nadeem#tsm#2024', 'HD1039'],
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
