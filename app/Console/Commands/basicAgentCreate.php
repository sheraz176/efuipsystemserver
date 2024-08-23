<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeleSalesAgent;
use App\Models\Company\CompanyProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            ['Mehwish', 'Asif', 'Mehwish.Asif.Basic.Sybrid', 'Mehwish#Asif#2024'],
            ['Shanza', 'Iqbal', 'Shanza.Iqbal.Basic.Sybrid', 'Shanza#Iqbal#2024'],
            ['Mubasher', 'Rehman', 'Mubasher.Rehman.Basic.Sybrid', 'Mubasher#Rehman#2024'],
            ['Muhammad', 'Bilal', 'Muhammad.Bilal.Basic.Sybrid', 'Muhammad#Bilal#2024'],
            ['Shumaila', 'Sanawar', 'Shumaila.Sanawar.Basic.Sybrid', 'Shumaila#Sanawar#2024'],
            ['Sardar', 'Farsat', 'Sardar.Farsat.Basic.Sybrid', 'Sardar#Farsat#2024'],
            ['M', 'Ehtesham', 'M.Ehtesham.Basic.Sybrid', 'Ehtesham#2024'],
            ['Ammar', 'Mehmood', 'Ammar.Mehmood.Basic.Sybrid', 'Ammar#Mehmood#2024'],
            ['Hanza', 'Shabir', 'Hanza.Shabir.Basic.Sybrid', 'Hanza#Shabir#2024'],
            ['Nayyab', 'Abbasi', 'Nayyab.Abbasi.Basic.Sybrid', 'Nayyab#Abbasi#2024'],
            ['Luqman', 'Shah', 'Luqman.Shah.Basic.Sybrid', 'Luqman#Shah#2024'],
            ['Ameer', 'Zafar', 'Ameer.Zafar.Basic.Sybrid', 'Ameer#Zafar#2024'],
            ['Yasir', 'Yasir Yaseen', 'Muhammad.YasirYaseen.Basic.Sybrid', 'Muhammad#Yasir#2024'],
            ['Talha', 'Talha', 'Muhammad.Talha.Basic.Sybrid', 'Muhammad#Talha#2024'],
            ['Dawood', 'Akhtar', 'Dawood.Akhtar.Basic.Sybrid', 'Dawood#Akhtar#2024'],
            ['Aman', 'Shamas', 'Aman.Shamas.Basic.Sybrid', 'Aman#Shamas#2024'],
            ['ayeshabasic', 'basic', 'Ayesha.Basic.Sybrid', 'Ayesha#2024'],
            ['Alishab', 'Irshad', 'Alishab.Irshad.Basic.Sybrid', 'Alishab#Irshad#2024'],
            ['Waleed', 'Zaman', 'Waleed.Zaman.Basic.Sybrid', 'Waleed#Zaman#2024'],
            ['Kashif', 'Abbasi', 'Kashif.Abbasi.Basic.Sybrid', 'Kashif#Abbasi#2024'],
            ['Sobia', 'Sarfaraz', 'Sobia.Sarfaraz.Basic.Sybrid', 'Sobia#Sarfaraz#2024'],
            ['Rimsha', 'Ishtiaq', 'Rimsha.Ishtiaq.Basic.Sybrid', 'Rimsha#Ishtiaq#2024'],
            ['shanzabasic', 'Rahim', 'Shanza.Rahim.Basic.Sybrid', 'Shanza#Rahim#2024'],
            ['Azra', 'Mustaqeem', 'Azra.Mustaqeem.Basic.Sybrid', 'Azra#Mustaqeem#2024'],
            ['Umar', 'Ilyas', 'Umar.Ilyas.Basic.Sybrid', 'Umar#Ilyas#2024'],
            ['Iqra', 'Amjad', 'Iqra.Amjad.Basic.Sybrid', 'Iqra#Amjad#2024'],
            ['Tayyaba', 'Talib', 'Tayyaba.Talib.Basic.Sybrid', 'Tayyaba#Talib#2024'],
            ['Sumbal', 'Murtaza', 'Sumbal.Murtaza.Basic.Sybrid', 'Sumbal#Murtaza#2024'],
            ['aamirbasic', 'Ali', 'Aamir.Ali.Basic.Sybrid', 'Aamir#Ali#2024'],
            ['Hussain', 'Tussadiq', 'Hussain.Tussadiq.Basic.Sybrid', 'Hussain#Tussadiq#2024'],
            ['Qurrat', 'ul Ain', 'Qurrat.ulAin.Basic.Sybrid', 'Qurrat#ulAin#2024'],
            ['Amna', 'Noor Fatima', 'Amna.NoorFatima.Basic.Sybrid', 'Amna#NoorFatima#2024'],
            ['Mahnoor', 'Fatima', 'Mahnoor.Fatima.Basic.Sybrid', 'Mahnoor#Fatima#2024'],
            ['Muneeb', 'Hassan', 'Muneeb.Hassan.Basic.Sybrid', 'Muneeb#Hassan#2024'],
            ['Huzaifa', 'Riwan', 'Huzaifa.Riwan.Basic.Sybrid', 'Huzaifa#Riwan#2024'],
            ['Aun', 'Abbas', 'Aun.Abbas.Basic.Sybrid', 'Aun#Abbas#2024'],
            ['Minhas', 'Ahmed', 'Minhas.Ahmed.Basic.Sybrid', 'Minhas#Ahmed#2024'],
            ['Ameerbasic', 'Hamza Khan', 'Ameer.HamzaKhan.Basic.Sybrid', 'Ameer#HamzaKhan#2024'],
            ['Hassan', 'Ali', 'Hassan.Ali.Basic.Sybrid', 'Hassan#Ali#2024'],
            ['shahzaibBasic', 'ali', 'Shahzaib.Basic.Sybrid', 'Shahzaib#2024']
        ];

         dd($agentsData);
        foreach ($agentsData as $data) {
            $request = [
                'first_name' => $data[0],
                'last_name' => $data[1],
                'username' => $data[2],
                'email' => strtolower($data[0]) . '@gmail.com',
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
