<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeleSalesAgent;
use App\Models\Company\CompanyProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class basicAgentCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'basicagent:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Basic Agent Ids Create';

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
    ['Muqadas', 'Dilawer', 'muqadas.dilawer.abacus', 'Muqadas#Dilawer##abacus#2025', 'muqadas.dilawer.abacus@gmail.com'],
    ['Akasha', 'Fatima', 'akasha.fatima.abacus', 'Akasha#Fatima##abacus#2025', 'akasha.fatima.abacus@gmail.com'],
    ['Alisha', 'Safdar', 'alisha.safdar.abacus', 'Alisha#Safdar##abacus#2025', 'alisha.safdar.abacus@gmail.com'],
    ['Mahnoor', 'Shahzadi', 'mahnoor.shahzadi.abacus', 'Mahnoor#Shahzadi##abacus#2025', 'mahnoor.shahzadi.abacus@gmail.com'],
    ['Maryam', 'Javed', 'maryam.javed.abacus', 'Maryam#Javed##abacus#2025', 'maryam.javed.abacus@gmail.com'],
    ['Hira', 'Azeem', 'hira.azeem.abacus', 'Hira#Azeem##abacus#2025', 'hira.azeem.abacus@gmail.com'],
    ['Sania', 'Khan', 'sania.khan.abacus', 'Sania#Khan##abacus#2025', 'sania.khan.abacus@gmail.com'],
    ['Maha', 'Jamal', 'maha.jamal.abacus', 'Maha#Jamal##abacus#2025', 'maha.jamal.abacus@gmail.com'],
    ['Iqra', 'Iqbal', 'iqra.iqbal.abacus', 'Iqra#Iqbal##abacus#2025', 'iqra.iqbal.abacus@gmail.com'],
    ['Rabia', 'Ashraf', 'rabia.ashraf.abacus', 'Rabia#Ashraf##abacus#2025', 'rabia.ashraf.abacus@gmail.com'],
    ['Muqadas', 'Naeem', 'muqadas.naeem.abacus', 'Muqadas#Naeem##abacus#2025', 'muqadas.naeem.abacus@gmail.com'],
];




        foreach ($agentsData as $data) {
            $request = [
                'first_name' => $data[0],
                'last_name' => $data[1],
                'username' => $data[2],
                'email' => $data[4],
                'status' => 1,
                'company_id' => 2,
                'password' => $data[3],
            ];

            $validator = Validator::make($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required|unique:tele_sales_agents',
                'email' => 'required|email|unique:tele_sales_agents',
                'status' => 'required|in:1,0',
                'company_id' => 'required',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                $this->error("Validation failed for: " . $request['username']);
                continue;
            }

            $validatedData = $validator->validated();
            $validatedData['islogin'] = 0;
            $validatedData['call_status'] = false;
            $validatedData['password'] = Hash::make($request['password']);
            $validatedData['today_login_time'] = now();
            $validatedData['today_logout_time'] = now();

            TelesalesAgent::create($validatedData);

            $this->info("Created Telesales Agent: " . $request['username']);
        }

        return 0;
    }
}
