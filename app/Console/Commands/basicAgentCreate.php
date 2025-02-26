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
            ['Maria', 'Safeer Abbasi', 'Maria.Safeer.Abbasi.sybrid', 'Maria#Safeer#Abbasi#sybrid#2025', 'abbasisidra0000@gmail.com'],
            ['Alishba', 'Fazal', 'Alishba.Fazal.sybrid', 'Alishba#Fazal#sybrid#2025', 'aamiralvi30@yahoo.com'],
            ['Laiba', 'Ashraf', 'Laiba.Ashraf.sybrid', 'Laiba#Ashraf#sybrid#2025', 'laibaashraf19@gmail.com'],
            ['Muhammad', 'Daniyal', 'Muhammad.Daniyal.sybrid', 'Muhammad#Daniyal#sybrid#2025', 'muhammaddaniyalaltaf044@gmail.com'],
            ['Faizan', 'Shaheen Abbasi', 'Faizan.Shaheen.Abbasi.sybrid', 'Faizan#Shaheen#Abbasi#sybrid#2025', 'faizanabbasi65757@gmail.com'],
            ['Serosh', 'Qaiser', 'Serosh.Qaiser.sybrid', 'Serosh#Qaiser#sybrid#2025', 'sehroshqaiser@gmail.com'],
            ['Matti', 'Ur Rehman', 'Matti.Ur.Rehman.sybrid', 'Matti#Ur#Rehman#sybrid#2025', 'ababa6369@gmail.com'],
            ['Ali', 'Muhammad', 'Ali.Muhammad.sybrid', 'Ali#Muhammad#sybrid#2025', 'alimuhammad1008557@gmail.com'],
            ['Fahad', 'Kazmi', 'Fahad.Kazmi.sybrid', 'Fahad#Kazmi#sybrid#2025', 'fahadkazmi545@gmail.com'],
            ['Mirfa', 'Riaz', 'Mirfa.Riaz.sybrid', 'Mirfa#Riaz#sybrid#2025', 'mariamariaaslam51@gmail.com'],
            ['Saqib', 'Abbasi', 'Saqib.Abbasi.sybrid', 'Saqib#Abbasi#sybrid#2025', 'sa6847@gmail.com'],
            ['Iqra', 'Javaid', 'Iqra.Javaid.sybrid', 'Iqra#Javaid#sybrid#2025', 'shykhiqra7@gmail.com']
        ];



        foreach ($agentsData as $data) {
            $request = [
                'first_name' => $data[0],
                'last_name' => $data[1],
                'username' => $data[2],
                'email' => $data[4],
                'status' => 1,
                'company_id' => 12,
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
