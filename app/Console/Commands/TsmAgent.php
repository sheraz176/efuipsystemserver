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
        ['Nosheen', 'Arzoo', 'Nosheen.Arzoo.tsm', 'Nosheen#Arzoo#tsm#2024', 'HD1141'],
        ['Hera', 'Shafaqat', 'Hera.Shafaqat.tsm', 'Hera#Shafaqat#tsm#2024', 'HD1142'],
        ['Abdullah', 'Arshad', 'Abdullah.Arshad.tsm', 'Abdullah#Arshad#tsm#2024', 'HD1143'],
        ['Tahir', 'Naeem', 'Tahir.Naeem.tsm', 'Tahir#Naeem#tsm#2024', 'HD1144'],
        ['Fatima', 'Aslam', 'Fatima.Aslam.tsm', 'Fatima#Aslam#tsm#2024', 'HD1145'],
        ['Zeenat', 'Eman', 'Zeenat.Eman.tsm', 'Zeenat#Eman#tsm#2024', 'HD1146'],
        ['Shiza', 'Dildar', 'Shiza.Dildar.tsm', 'Shiza#Dildar#tsm#2024', 'HD1147'],
        ['Neha', 'Aslam', 'Neha.Aslam.tsm', 'Neha#Aslam#tsm#2024', 'HD1148'],
        ['Zahra', 'Aftab', 'Zahra.Aftab.tsm', 'Zahra#Aftab#tsm#2024', 'HD1149'],
        ['Wajiha', 'Ali', 'Wajiha.Ali.tsm', 'Wajiha#Ali#tsm#2024', 'HD1150'],
        ['Maliha', 'Khan', 'Maliha.Khan.tsm', 'Maliha#Khan#tsm#2024', 'HD1151'],
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
