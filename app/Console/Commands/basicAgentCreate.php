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
            ['Mariam', 'Jabbar', 'Mariam.Jabbar.abacus', 'Mariam#Jabbar#abacus#2025', 'Mariam.Jabbar.abacus@gmail.com'],
            ['Rimsha', 'Arif', 'Rimsha.Arif.abacus', 'Rimsha#Arif#abacus#2025', 'Rimsha.Arif.abacus@gmail.com'],
            ['Mahnoor', 'Vicky', 'Mahnoor.Vicky.abacus', 'Mahnoor#Vicky#abacus#2025', 'Mahnoor.Vicky.abacus@gmail.com'],
            ['Laiba', 'Manzoor', 'Laiba.Manzoor.abacus', 'Laiba#Manzoor#abacus#2025', 'Laiba.Manzoor.abacus@gmail.com'],
            ['Zainab', 'Anwar', 'Zainab.Anwar.abacus', 'Zainab#Anwar#abacus#2025', 'Zainab.Anwar.abacus@gmail.com'],
            ['Saba', 'Shoukat', 'Saba.Shoukat.abacus', 'Saba#Shoukat#abacus#2025', 'Saba.Shoukat.abacus@gmail.com'],
            ['Ayesha', 'Tariq', 'Ayesha.Tariq.abacus', 'Ayesha#Tariq#abacus#2025', 'Ayesha.Tariq.abacus@gmail.com'],
            ['Nagina', 'Shahzadi', 'Nagina.Shahzadi.abacus', 'Nagina#Shahzadi#abacus#2025', 'Nagina.Shahzadi.abacus@gmail.com'],
            ['Zoya', 'Murad', 'Zoya.Murad.abacus', 'Zoya#Murad#abacus#2025', 'Zoya.Murad.abacus@gmail.com'],
            ['Iqra', 'Shahzadi', 'Iqra.Shahzadi.abacus', 'Iqra#Shahzadi#abacus#2025', 'Iqra.Shahzadi.abacus@gmail.com'],
            ['Zainab', 'Ashraf', 'Zainab.Ashraf.abacus', 'Zainab#Ashraf#abacus#2025', 'Zainab.Ashraf.abacus@gmail.com'],
            ['Aqsa', 'Fatima', 'Aqsa.Fatima.abacus', 'Aqsa#Fatima#abacus#2025', 'Aqsa.Fatima.abacus@gmail.com'],
            ['Saleha', 'Naseem', 'Saleha.Naseem.abacus', 'Saleha#Naseem#abacus#2025', 'Saleha.Naseem.abacus@gmail.com'],
            ['Aiman', 'Naeem', 'Aiman.Naeem.abacus', 'Aiman#Naeem#abacus#2025', 'Aiman.Naeem.abacus@gmail.com'],
            ['Aroosa', 'Khalid', 'Aroosa.Khalid.abacus', 'Aroosa#Khalid#abacus#2025', 'Aroosa.Khalid.abacus@gmail.com'],
            ['Kaukab', 'Talib', 'Kaukab.Talib.abacus', 'Kaukab#Talib#abacus#2025', 'Kaukab.Talib.abacus@gmail.com'],
            ['Muskan', 'Kawal', 'Muskan.Kawal.abacus', 'Muskan#Kawal#abacus#2025', 'Muskan.Kawal.abacus@gmail.com'],
            ['Noor', 'Ul Huda', 'Noor.Ul.Huda.abacus', 'Noor#Ul#Huda#abacus#2025', 'Noor.Ul.Huda.abacus@gmail.com'],
            ['Zarish', 'Saleem', 'Zarish.Saleem.abacus', 'Zarish#Saleem#abacus#2025', 'Zarish.Saleem.abacus@gmail.com'],
            ['Aleeza', 'Shahzad', 'Aleeza.Shahzad.abacus', 'Aleeza#Shahzad#abacus#2025', 'Aleeza.Shahzad.abacus@gmail.com'],
            ['Sania', 'Nazir', 'Sania.Nazir.abacus', 'Sania#Nazir#abacus#2025', 'Sania.Nazir.abacus@gmail.com']
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
