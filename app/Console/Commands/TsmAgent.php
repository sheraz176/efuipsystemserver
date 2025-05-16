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
        ['Wajiha', 'Qureshi', 'Wajiha.Qureshi.tsm.2025', 'Wajiha#Qureshi#tsm#2025', 'HD162'],
        ['Ayesha', 'Mehmood', 'Ayesha.Mehmood.tsm.2025', 'Ayesha#Mehmood#tsm#2025', 'HD446'],
        ['Sajjad', 'Ali', 'Sajjad.Ali.tsm.2025', 'Sajjad#Ali#tsm#2025', 'HD727'],
        ['Anita', 'Kanwel', 'Anita.Kanwel.tsm.2025', 'Anita#Kanwel#tsm#2025', 'HD772'],
        ['Tasmia', 'Khan', 'Tasmia.Khan.tsm.2025', 'Tasmia#Khan#tsm#2025', 'EP-36'],
        ['Kinza', 'Mushtaq', 'Kinza.Mushtaq.tsm.2025', 'Kinza#Mushtaq#tsm#2025', 'HD805'],
        ['Hamza', 'Arshad', 'Hamza.Arshad.tsm.2025', 'Hamza#Arshad#tsm#2025', 'HD1016'],
        ['Roma', 'Sadiq', 'Roma.Sadiq.tsm.2025', 'Roma#Sadiq#tsm#2025', 'HD1020'],
        ['Komal', 'two', 'Komal.two.tsm.2025', 'Komal##tsm#2025', 'HD1169'],
        ['Mehak', 'Riaz', 'Mehak.Riaz.tsm.2025', 'Mehak#Riaz#tsm#2025', 'HD1174'],
        ['Nayyer', 'Usman', 'Nayyer.Usman.tsm.2025', 'Nayyer#Usman#tsm#2025', 'HD1197'],
        ['Zohaib', 'Hassan', 'Zohaib.Hassan.tsm.2025', 'Zohaib#Hassan#tsm#2025', 'HD1198'],
        ['Mubashra', 'Tofeeq', 'Mubashra.Tofeeq.tsm.2025', 'Mubashra#Tofeeq#tsm#2025', 'HD1223'],
        ['Ayesha', 'Jan', 'Ayesha.Jan.tsm.2025', 'Ayesha#Jan#tsm#2025', 'HD1231'],
        ['Kiran', 'Akeem', 'Kiran.Akeem.tsm.2025', 'Kiran#Akeem#tsm#2025', 'HD1225'],
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
