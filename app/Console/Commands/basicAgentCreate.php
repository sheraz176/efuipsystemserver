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
            ['Rafia', 'Nizam', 'Rafia.Nizam.Waada', 'Rafia#Nizam#Waada#2025', 'rafia.nizam.waada@gmail.com'],
            ['Talha', 'Shahbaz', 'Talha.Shahbaz.Waada', 'Talha#Shahbaz#Waada#2025', 'talha.shahbaz.waada@gmail.com'],
            ['Nimra', 'Khalid', 'Nimra.Khalid.Waada', 'Nimra#Khalid#Waada#2025', 'nimra.khalid.waada@gmail.com'],
            ['Mehak', 'Makesh', 'Mehak.Makesh.Waada', 'Mehak#Makesh#Waada#2025', 'mehak.mukesh.waada@gmail.com'],
            ['Sheeza', 'Noor', 'Sheeza.Noor.Waada', 'Sheeza#Noor#Waada#2025', 'sheeza.noor10.waada@gmail.com'],
            ['Mehreen', 'Zahid', 'Mehreen.Zahid.Waada', 'Mehreen#Zahid#Waada#2025', 'mehreen06.zahid.waada@gmail.com'],
            ['Shabana', 'Bibi', 'Shabana.Bibi.Waada', 'Shabana#Bibi#Waada#2025', 'shabana.zahoor.waada@gmail.com'],
            ['Fareeha', 'Ahmed', 'Fareeha.Ahmed.Waada', 'Fareeha#Ahmed#Waada#2025', 'fareha13.ahmed.waada@gmail.com'],
            ['Mahrukh', 'Hanif', 'Mahrukh.Hanif.Waada', 'Mahrukh#Hanif#Waada#2025', 'mahrukh.hanif.waada@gmail.com'],
            ['Asma', 'Siddique', 'Asma.Siddique.Waada', 'Asma#Siddique#Waada#2025', 'asma.siddique.waada@gmail.com'],
            ['Mohsin', 'Khan', 'Mohsin.Khan.Waada', 'Mohsin#Khan#Waada#2025', 'mohsin.khan.waada@gmail.com'],
            ['Mujahid', 'Bilal', 'Mujahid.Bilal.Waada', 'Mujahid#Bilal#Waada#2025', 'mujahid.bilal.waada@gmail.com'],
            ['Ayesha', 'Nadeem', 'Ayesha.Nadeem.Waada', 'Ayesha#Nadeem#Waada#2025', 'ayesha.nadeem01.waada@gmail.com'],
            ['Hira', 'Miraj', 'Hira.Miraj.Waada', 'Hira#Miraj#Waada#2025', 'Hira.miraj.waada@gmail.com'],
            ['Zahira', 'Khan', 'Zahira.Khan.Waada', 'Zahira#Khan#Waada#2025', 'zahira.khan.waada@gmail.com'],
        ];





        foreach ($agentsData as $data) {
            $request = [
                'first_name' => $data[0],
                'last_name' => $data[1],
                'username' => $data[2],
                'email' => $data[4],
                'status' => 1,
                'company_id' => 19,
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
