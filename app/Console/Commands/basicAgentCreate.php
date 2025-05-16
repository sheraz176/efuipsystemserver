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
            ['Muhammad Usman', 'Muhammad Sarwar', 'Muhammad.Usman.Waada', 'Muhammad#Usman#Waada#2025', 'usman.sarwar.waada@gmail.com'],
            ['Usama', 'Ahmed', 'Usama.Ahmed.Waada', 'Usama#Ahmed#Waada#2025', 'usama.ahmed.waada@gmail.com'],
            ['Nimra', 'Anees', 'Nimra.Anees.Waada', 'Nimra#Anees#Waada#2025', 'nimra.anis.waada@gmal.com'],
            ['Muhammad Humza', 'Junaid Jilani', 'MuhammadHumza.JunaidJilani.Waada', 'MuhammadHumza#JunaidJilani#Waada#2025', 'Humza.jilani.waada@gmail.com'],
            ['Komal', 'Riaz', 'Komal.Riaz.Waada', 'Komal#Riaz#Waada#2025', 'komal1.riaz.waada@gmail.com'],
            ['Hadiqa', 'Anees', 'Hadiqa.Anees.Waada', 'Hadiqa#Anees#Waada#2025', 'hadiqa.anis.waada@gmail.com'],
            ['Muhammad Sumair', 'Shamim Iqbal', 'MuhammadSumair.ShamimIqbal.Waada', 'MuhammadSumair#ShamimIqbal#Waada#2025', 'm.sumairkhan.waada@gmail.com'],
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
