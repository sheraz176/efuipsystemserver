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
    ['Muskan', 'Fatima', 'muskan.fatima.abacus', 'Muskan#Fatima##abacus#2026', 'muskan.fatima@gmail.com'],
    ['Areesha', 'Naeem', 'areesha.naeem.abacus', 'Areesha#Naeem##abacus#2026', 'areesha.naeem@gmail.com'],
    ['Javaria', 'Jamil', 'javaria.jamil.abacus', 'Javaria#Jamil##abacus#2026', 'javaria.jamil@gmail.com'],
    ['Faria', 'Ashraf', 'faria.ashraf.abacus', 'Faria#Ashraf##abacus#2026', 'faria.ashraf@gmail.com'],
    ['Tasbeeha', 'Shahzadi', 'tasbeeha.shahzadi.abacus', 'Tasbeeha#Shahzadi##abacus#2026', 'tasbeeha.shahzadi@gmail.com'],
    ['Nimra', 'Bukhari', 'nimra.bukhari.abacus', 'Nimra#Bukhari##abacus#2026', 'nimra.bukhari@gmail.com'],
    ['Maiyla', 'Mukhtar', 'maiyla.mukhtar.abacus', 'Maiyla#Mukhtar##abacus#2026', 'maiyla.mukhtar@gmail.com'],
    ['Eisha', 'Iftikhar', 'eisha.iftikhar.abacus', 'Eisha#Iftikhar##abacus#2026', 'eisha.iftikhar@gmail.com'],
    ['Alisha', 'Tasleem', 'alisha.tasleem.abacus', 'Alisha#Tasleem##abacus#2026', 'alisha.tasleem@gmail.com'],
    ['Sana', 'Faisal', 'sana.faisal.abacus', 'Sana#Faisal##abacus#2026', 'sana.faisal@gmail.com'],
    ['Syeda', 'Attiqa', 'syeda.attiqa.abacus', 'Syeda#Attiqa##abacus#2026', 'syeda.attiqa@gmail.com'],
    ['Amna', 'Noor', 'amna.noor.abacus', 'Amna#Noor##abacus#2026', 'amna.noor@gmail.com'],
    ['Muqadas', 'Ashraf', 'muqadas.ashraf.abacus', 'Muqadas#Ashraf##abacus#2026', 'muqadas.ashraf@gmail.com'],
    ['Komal', 'Shahzadi', 'komal.shahzadi.abacus', 'Komal#Shahzadi##abacus#2026', 'komal.shahzadi@gmail.com'],
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
