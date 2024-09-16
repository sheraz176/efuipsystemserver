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
            ['Abdul', 'Basit', 'Abdul.Basit.sybrid', 'Abdul#Basit#sybrid#2024', 'basitkhataka@gmail.com'],
            ['Ayyaz', 'Hussain', 'Ayyaz.Hussain.sybrid', 'Ayyaz#Hussain#sybrid#2024', 'ayyaz1735@gmail.com'],
            ['Muhammad', 'Sameed Khan', 'Muhammad.SameedKhan.sybrid', 'Muhammad#SameedKhan#sybrid#2024', 'sameedkapoor1415@gmail.com'],
            ['Muhammad', 'Shahid', 'Muhammad.Shahid.sybrid', 'Muhammad#Shahid#sybrid#2024', 'istid4all@gmail.com'],
            ['Muhammad', 'Rohail', 'Muhammad.Rohail.sybrid', 'Muhammad#Rohail#sybrid#2024', 'khanrohail760@gmail.com'],
            ['Farhan', 'Ali', 'Farhan.Ali.sybrid', 'Farhan#Ali#sybrid#2024', 'farhanali4t5@gmail.com'],
            ['Ghulam', 'Kibriya', 'Ghulam.Kibriya.sybrid', 'Ghulam#Kibriya#sybrid#2024', 'sameerirfan323@gmail.com'],
            ['Iftikhar', 'Ul Haq', 'Iftikhar.UlHaq.sybrid', 'Iftikhar#UlHaq#sybrid#2024', 'ifti5114@gmail.com'],
            ['Muhammad', 'Shakeel', 'Muhammad.Shakeel.sybrid', 'Muhammad#Shakeel#sybrid#2024', 'bangashshakeel3@gmail.com'],
            ['Hamza', 'Hassan Khan', 'Hamza.HassanKhan.sybrid', 'Hamza#HassanKhan#sybrid#2024', 'humzahk28@gmail.com'],
            ['Suleman', 'Khan', 'Suleman.Khan.sybrid', 'Suleman#Khan#sybrid#2024', 'sulimankhankhan12345@gmail.com'],
            ['Hamza', 'Riaz', 'Hamza.Riaz.sybrid', 'Hamza#Riaz#sybrid#2024', 'riazhamza222@gmail.com'],
            ['Muhammad', 'Hammad Khan', 'Muhammad.HammadKhan.sybrid', 'Muhammad#HammadKhan#sybrid#2024', 'hammadkhan2977@gmail.com'],
            ['Muhammad', 'Afrasiyab', 'Muhammad.Afrasiyab.sybrid', 'Muhammad#Afrasiyab#sybrid#2024', 'afrasiyab.ctl@gmail.com'],
            ['Aman', 'Ullah', 'Aman.Ullah.sybrid', 'Aman#Ullah#sybrid#2024', 'ak3719864@gmail.com'],
            ['Muhammad', 'Ammad', 'Muhammad.Ammad.sybrid', 'Muhammad#Ammad#sybrid#2024', 'muhammadammad9584@gmail.com'],
            ['Ahsan', 'Masood', 'Ahsan.Masood.sybrid', 'Ahsan#Masood#sybrid#2024', 'ahsanmasood984@gmail.com'],
            ['Shahmeer', 'Hussain', 'Shahmeer.Hussain.sybrid', 'Shahmeer#Hussain#sybrid#2024', 'shahmeerhussain18@gmail.com'],
            ['Abdur', 'Rehman Abid', 'Abdur.RehmanAbid.sybrid', 'Abdur#RehmanAbid#sybrid#2024', 'leaderoforganic@gmail.com'],
            ['Bashir', 'Ahmed Raja', 'Bashir.AhmedRaja.sybrid', 'Bashir#AhmedRaja#sybrid#2024', 'ahmedmujahid0066@gmail.com'],
            ['Muhammad', 'Yousaf', 'Muhammad.Yousaf.sybrid', 'Muhammad#Yousaf#sybrid#2024', 'melucky905@gmail.com'],
            ['Ihsan', 'Ullah', 'Ihsan.Ullah.sybrid', 'Ihsan#Ullah#sybrid#2024', 'ihsanakhan55@gmail.com'],
            ['Rizwan', 'ur Rehman', 'Rizwan.Rehman.sybrid', 'Rizwan#Rehman#sybrid#2024', 'rizwankhan0566322@gmail.com'],
            ['Salman', 'Muhammad', 'Salman.Muhammad.sybrid', 'Salman#Muhammad#sybrid#2024', 'bangashsalman10@gmail.com'],
            ['Jannat', 'Bibi', 'Jannat.Bibi.sybrid', 'Jannat#Bibi#sybrid#2024', 'jannatmanzoor700@gmail.com'],
            ['Rabia', 'Zubair', 'Rabia.Zubair.sybrid', 'Rabia#Zubair#sybrid#2024', 'rm4190048@gmail.com'],
            ['Isma', 'Aftab', 'Isma.Aftab.sybrid', 'Isma#Aftab#sybrid#2024', 'Ismaaftab702@gmail.com']
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
