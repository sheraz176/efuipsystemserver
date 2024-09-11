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
            ['Areeba', 'Amir', 'Areeba.Amir.ibex', 'Areeba#Amir#ibex#2024', 'areeba.amir@ibex.co'],
            ['Irum', 'Pervaiz', 'Irum.Pervaiz.ibex', 'Irum#Pervaiz#ibex#2024', 'irum.pervaiz@ibex.co'],
            ['Laraib', 'Nafees', 'Laraib.Nafees.ibex', 'Laraib#Nafees#ibex#2024', 'laraib.nafees1@ibex.co'],
            ['Zahra', 'Batool', 'Zahra.Batool.ibex', 'Zahra#Batool#ibex#2024', 'zahra.batool1@ibex.co'],
            ['Zainab', 'Ejaz', 'Zainab.Ejaz.ibex', 'Zainab#Ejaz#ibex#2024', 'zainab.ejaz@ibex.co'],
            ['Aiman', 'Zakri', 'Aiman.Zakri.ibex', 'Aiman#Zakri#ibex#2024', 'aiman.zakri@ibex.co'],
            ['Sadia', 'Noreen', 'Sadia.Noreen.ibex', 'Sadia#Noreen#ibex#2024', 'sadia.noreen2@ibex.co'],
            ['Samreen', 'Naz', 'Samreen.Naz.ibex', 'Samreen#Naz#ibex#2024', 'samreen.naz@ibex.co'],
            ['Nadia', 'Shafique', 'Nadia.Shafique.ibex', 'Nadia#Shafique#ibex#2024', 'nadia.shafique@ibex.co'],
            ['NoureenIbex', 'Ibex', 'NoureenIbex.ibex', 'Noureen##ibex#2024', 'noureenIbex@ibex.co'],
            ['Qurat', 'ul Ain', 'Qurat.ulAin.ibex', 'Qurat#ulAin#ibex#2024', 'qurat.ain3@ibex.co'],
            ['Syeda', 'Mariam', 'Syeda.Mariam.ibex', 'Syeda#Mariam#ibex#2024', 'syeda.mariam@ibex.co'],
            ['Tayyaba', 'Riaz', 'Tayyaba.Riaz.ibex', 'Tayyaba#Riaz#ibex#2024', 'tayyaba.riaz@ibex.co'],
            ['EmanIbex', 'Ibex', 'EmanIbex.ibex', 'Eman##ibex#2024', 'emanIbex@ibex.co'],
            ['Kunzul', 'Iman', 'Kunzul.Iman.ibex', 'Kunzul#Iman#ibex#2024', 'kunzul.iman@ibex.co'],
            ['Maryam', 'Khalid', 'Maryam.Khalid.ibex', 'Maryam#Khalid#ibex#2024', 'maryam.khalid@ibex.co'],
            ['Sania', 'Abid', 'Sania.Abid.ibex', 'Sania#Abid#ibex#2024', 'sania.abid@ibex.co'],
            ['Anum', 'Adnan', 'Anum.Adnan.ibex', 'Anum#Adnan#ibex#2024', 'anum.adnan@ibex.co'],
            ['Hamiyal', 'Shah', 'Hamiyal.Shah.ibex', 'Hamiyal#Shah#ibex#2024', 'hamiyal.shah@ibex.co'],
            ['Khadija', 'Bibi', 'Khadija.Bibi.ibex', 'Khadija#Bibi#ibex#2024', 'khadija.bibi1@ibex.co'],
            ['Aliza', 'Asif', 'Aliza.Asif.ibex', 'Aliza#Asif#ibex#2024', 'aliza.qureshi@ibex.co'],
            ['Alina', 'Chouhdry', 'Alina.Chouhdry.ibex', 'Alina#Chouhdry#ibex#2024', 'alina.chouhdry@ibex.co'],
            ['Sobia', 'Aqeel', 'Sobia.Aqeel.ibex', 'Sobia#Aqeel#ibex#2024', 'sobia.aqeel@ibex.co'],
            ['Romasa', 'Feroze', 'Romasa.Feroze.ibex', 'Romasa#Feroze#ibex#2024', 'romasa.feroze@ibex.co'],
        ];





        foreach ($agentsData as $data) {
            $request = [
                'first_name' => $data[0],
                'last_name' => $data[1],
                'username' => $data[2],
                'email' => $data[4],
                'status' => 1,
                'company_id' => 1,
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
