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
            ['Maheen', 'Afzal', 'Maheen.Afzal.abacus', 'Maheen#Afzal#abacus#2025', 'Maheen.Afzal.abacus@gmail.com'],
            ['Sania', 'Sajid', 'Sania.Sajid.abacus', 'Sania#Sajid#abacus#2025', 'Sania.Sajid.abacus@gmail.com'],
            ['Parwasha', 'Ashiq', 'Parwasha.Ashiq.abacus', 'Parwasha#Ashiq#abacus#2025', 'Parwasha.Ashiq.abacus@gmail.com'],
            ['Amna', 'Bibi', 'Amna.Bibi.abacus', 'Amna#Bibi#abacus#2025', 'Amna.Bibi.abacus@gmail.com'],
            ['Noor', 'Fayyaz', 'Noor.Fayyaz.abacus', 'Noor#Fayyaz#abacus#2025', 'Noor.Fayyaz.abacus@gmail.com'],
            ['Laila', 'Eram Saeed', 'Laila.EramSaeed.abacus', 'Laila#EramSaeed#abacus#2025', 'Laila.EramSaeed.abacus@gmail.com'],
            ['Misbah', 'Shabbir', 'Misbah.Shabbir.abacus', 'Misbah#Shabbir#abacus#2025', 'Misbah.Shabbir.abacus@gmail.com'],
            ['Syeda Sadia', 'Mansoor', 'SyedaSadia.Mansoor.abacus', 'SyedaSadia#Mansoor#abacus#2025', 'SyedaSadia.Mansoor.abacus@gmail.com'],
            ['Alishba', 'Akram', 'Alishba.Akram.abacus', 'Alishba#Akram#abacus#2025', 'Alishba.Akram.abacus@gmail.com'],
            ['Maha', 'Batool', 'Maha.Batool.abacus', 'Maha#Batool#abacus#2025', 'Maha.Batool.abacus@gmail.com'],
            ['Nida', 'Iqbal', 'Nida.Iqbal.abacus', 'Nida#Iqbal#abacus#2025', 'Nida.Iqbal.abacus@gmail.com'],
            ['Laiba', 'Noor', 'Laiba.Noor.abacus', 'Laiba#Noor#abacus#2025', 'Laiba.Noor.abacus@gmail.com'],
            ['Imra', 'Bukhari', 'Imra.Bukhari.abacus', 'Imra#Bukhari#abacus#2025', 'Imra.Bukhari.abacus@gmail.com'],
            ['Nimra', 'Idress', 'Nimra.Idress.abacus', 'Nimra#Idress#abacus#2025', 'Nimra.Idress.abacus@gmail.com'],
            ['Saba', 'Shahzadi', 'Saba.Shahzadi.abacus', 'Saba#Shahzadi#abacus#2025', 'Saba.Shahzadi.abacus@gmail.com'],
            ['Hafiza Ayesha', 'Imtiaz', 'HafizaAyesha.Imtiaz.abacus', 'HafizaAyesha#Imtiaz#abacus#2025', 'HafizaAyesha.Imtiaz.abacus@gmail.com'],
            ['Taskeen', 'Zaman', 'Taskeen.Zaman.abacus', 'Taskeen#Zaman#abacus#2025', 'Taskeen.Zaman.abacus@gmail.com'],
            ['Eman', 'Talib', 'Eman.Talib.abacus', 'Eman#Talib#abacus#2025', 'Eman.Talib.abacus@gmail.com'],
            ['Minahil', 'Liaqat', 'Minahil.Liaqat.abacus', 'Minahil#Liaqat#abacus#2025', 'Minahil.Liaqat.abacus@gmail.com'],
            ['Fareeha', 'Bashir', 'Fareeha.Bashir.abacus', 'Fareeha#Bashir#abacus#2025', 'Fareeha.Bashir.abacus@gmail.com'],
            ['Fatima', 'Tabbasum', 'Fatima.Tabbasum.abacus', 'Fatima#Tabbasum#abacus#2025', 'Fatima.Tabbasum.abacus@gmail.com']
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
