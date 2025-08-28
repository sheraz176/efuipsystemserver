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
    ['Tashifa', 'Shahbaz', 'tashifa.shahbaz.abacus', 'Tashifa#Shahbaz##abacus#2025', 'tashifa.shahbaz@abacus.co'],
    ['Mehak', 'Kamran', 'mehak.kamran.abacus', 'Mehak#Kamran##abacus#2025', 'mehak.kamran@abacus.co'],
    ['Maha', 'Al Shabib', 'maha.alshabib.abacus', 'Maha#Al Shabib##abacus#2025', 'maha.alshabib@abacus.co'],
    ['Anusha', 'Qayyum', 'anusha.qayyum.abacus', 'Anusha#Qayyum##abacus#2025', 'anusha.qayyum@abacus.co'],
    ['Malaika', 'Riaz', 'malaika.riaz.abacus', 'Malaika#Riaz##abacus#2025', 'malaika.riaz@abacus.co'],
    ['Farkhanda', 'Jabeen', 'farkhanda.jabeen.abacus', 'Farkhanda#Jabeen##abacus#2025', 'farkhanda.jabeen@abacus.co'],
    ['Saleha', 'Shehzadi', 'saleha.shehzadi.abacus', 'Saleha#Shehzadi##abacus#2025', 'saleha.shehzadi@abacus.co'],
    ['Iqra', 'Dawood', 'iqra.dawood.abacus', 'Iqra#Dawood##abacus#2025', 'iqra.dawood@abacus.co'],
    ['Subeen', 'Fatima', 'subeen.fatima.abacus', 'Subeen#Fatima##abacus#2025', 'subeen.fatima@abacus.co'],
    ['Sheeza', 'Abbas', 'sheeza.abbas.abacus', 'Sheeza#Abbas##abacus#2025', 'sheeza.abbas@abacus.co'],
    ['Malaika', 'Munir', 'malaika.munir.abacus', 'Malaika#Munir##abacus#2025', 'malaika.munir@abacus.co'],
    ['Ifra', 'Ashfaq', 'ifra.ashfaq.abacus', 'Ifra#Ashfaq##abacus#2025', 'ifra.ashfaq@abacus.co'],
    ['Sidra', 'Khursheed', 'sidra.khursheed.abacus', 'Sidra#Khursheed##abacus#2025', 'sidra.khursheed@abacus.co'],
    ['Zainab', 'Imran', 'zainab.imran.abacus', 'Zainab#Imran##abacus#2025', 'zainab.imran@abacus.co'],
    ['Memoona', 'Sabir', 'memoona.sabir.abacus', 'Memoona#Sabir##abacus#2025', 'memoona.sabir@abacus.co'],
    ['Isha', 'Athar Shah', 'isha.atharshah.abacus', 'Isha#Athar Shah##abacus#2025', 'isha.atharshah@abacus.co'],
    ['Taniya', 'Qadeer Ahmad', 'taniya.qadeerahmad.abacus', 'Taniya#Qadeer Ahmad##abacus#2025', 'taniya.qadeerahmad@abacus.co'],
    ['Areeba', 'Ilyaz', 'areeba.ilyaz.abacus', 'Areeba#Ilyaz##abacus#2025', 'areeba.ilyaz@abacus.co'],
    ['Esha', 'Ahsan', 'esha.ahsan.abacus', 'Esha#Ahsan##abacus#2025', 'esha.ahsan@abacus.co'],
    ['Rabia', 'Iqbal', 'rabia.iqbal.abacus', 'Rabia#Iqbal##abacus#2025', 'rabia.iqbal@abacus.co'],
    ['Nuzhat', 'Waseem', 'nuzhat.waseem.abacus', 'Nuzhat#Waseem##abacus#2025', 'nuzhat.waseem@abacus.co'],
    ['Ramsha', 'Khan', 'ramsha.khan.abacus', 'Ramsha#Khan##abacus#2025', 'ramsha.khan@abacus.co'],
    ['Syeda', 'Memona', 'syeda.memona.abacus', 'Syeda#Memona##abacus#2025', 'syeda.memona@abacus.co'],
    ['Rahima', 'Imran', 'rahima.imran.abacus', 'Rahima#Imran##abacus#2025', 'rahima.imran@abacus.co'],
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
