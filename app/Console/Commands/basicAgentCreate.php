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
    ['Ghulam', 'Ali', 'ghulam.ali.sybrid', 'Ghulam#Ali##sybrid#2025', 'ghulam.ali@sybrid.co'],
    ['Muhammad', 'Subhan', 'muhammad.subhan.sybrid', 'Muhammad#Subhan##sybrid#2025', 'muhammad.subhan@sybrid.co'],
    ['Saliha', 'Qayyum', 'saliha.qayyum.sybrid', 'Saliha#Qayyum##sybrid#2025', 'saliha.qayyum@sybrid.co'],
    ['Rabbia', 'Ahmad', 'rabbia.ahmad.sybrid', 'Rabbia#Ahmad##sybrid#2025', 'rabbia.ahmad@sybrid.co'],
    ['Muhammad', 'Usman2', 'muhammad.usman22.sybrid', 'Muhammad#Usman##sybrid#2025', 'muhammad.usman22@sybrid.co'],
    ['Sadaf', 'Noor', 'sadaf.noor.sybrid', 'Sadaf#Noor##sybrid#2025', 'sadaf.noor@sybrid.co'],
    ['Muhammad', 'Salman', 'muhammad.salman.sybrid', 'Muhammad#Salman##sybrid#2025', 'muhammad.salman@sybrid.co'],
    ['Usama', 'Keyani', 'usama.keyani.sybrid', 'Usama#Keyani##sybrid#2025', 'usama.keyani@sybrid.co'],
    ['Tubessum', 'Moheen', 'tubessum.moheen.sybrid', 'Tubessum#Moheen##sybrid#2025', 'tubessum.moheen@sybrid.co'],
    ['Sawera', 'Shahid', 'sawera.shahid.sybrid', 'Sawera#Shahid##sybrid#2025', 'sawera.shahid@sybrid.co'],
    ['Rayyan', 'Imran', 'rayyan.imran.sybrid', 'Rayyan#Imran##sybrid#2025', 'rayyan.imran@sybrid.co'],
    ['Ali', 'Rajpoot', 'ali.rajpoot.sybrid', 'Ali#Rajpoot##sybrid#2025', 'ali.rajpoot@sybrid.co'],
    ['Imran', 'Ali', 'imran.ali.sybrid', 'Imran#Ali##sybrid#2025', 'imran.ali@sybrid.co'],
    ['Ghulam', 'Fareed', 'ghulam.fareed.sybrid', 'Ghulam#Fareed##sybrid#2025', 'ghulam.fareed@sybrid.co'],
    ['Zeesham', 'Ilyas', 'zeesham.ilyas.sybrid', 'Zeesham#Ilyas##sybrid#2025', 'zeesham.ilyas@sybrid.co'],
    ['Usman', 'Asad', 'usman.asad.sybrid', 'Usman#Asad##sybrid#2025', 'usman.asad@sybrid.co'],
    ['Alishba', 'Naveed', 'alishba.naveed.sybrid', 'Alishba#Naveed##sybrid#2025', 'alishba.naveed@sybrid.co'],
    ['Nisba', 'Waheed', 'nisba.waheed.sybrid', 'Nisba#Waheed##sybrid#2025', 'nisba.waheed@sybrid.co'],
    ['Sadia', 'Jameel', 'sadia.jameel.sybrid', 'Sadia#Jameel##sybrid#2025', 'sadia.jameel@sybrid.co'],
    ['Hammad', 'Ahmed', 'hammad.ahmed.sybrid', 'Hammad#Ahmed##sybrid#2025', 'hammad.ahmed@sybrid.co'],
    ['Muhammad', 'Haseeb22', 'muhammad.haseeb222.sybrid', 'Muhammad#Haseeb##sybrid#2025', 'muhammad.2haseeb@sybrid.co'],
    ['Muhammad', 'Noor', 'muhammad.noor.sybrid', 'Muhammad#Noor##sybrid#2025', 'muhammad.noor@sybrid.co'],
    ['Laiba', 'Akram', 'laiba.akram.sybrid', 'Laiba#Akram##sybrid#2025', 'laiba.akram@sybrid.co'],
    ['Ahmed', 'Abdullah', 'ahmed.abdullah.sybrid', 'Ahmed#Abdullah##sybrid#2025', 'ahmed.abdullah@sybrid.co'],
    ['Sohail', 'Shah', 'sohail.shah.sybrid', 'Sohail#Shah##sybrid#2025', 'sohail.shah@sybrid.co'],
    ['Ghazanfar', 'Ali', 'ghazanfar.ali.sybrid', 'Ghazanfar#Ali##sybrid#2025', 'ghazanfar.ali@sybrid.co'],
    ['Sana', 'Aslam', 'sana.aslam.sybrid', 'Sana#Aslam##sybrid#2025', 'sana.aslam@sybrid.co'],
    ['Abdul', 'Moiz', 'abdul.moiz.sybrid', 'Abdul#Moiz##sybrid#2025', 'abdul.moiz@sybrid.co'],
    ['Naveed', 'Khan', 'naveed.khan.sybrid', 'Naveed#Khan##sybrid#2025', 'naveed.khan@sybrid.co'],
    ['Alishba', 'Tahir', 'alishba.tahir.sybrid', 'Alishba#Tahir##sybrid#2025', 'alishba.tahir@sybrid.co'],
    ['Afzaal', 'Aslam', 'afzaal.aslam.sybrid', 'Afzaal#Aslam##sybrid#2025', 'afzaal.aslam@sybrid.co'],
    ['Noor', 'Fatima', 'noor.fatima.sybrid', 'Noor#Fatima##sybrid#2025', 'noor.fatima@sybrid.co'],
    ['Saim', 'Shahzad', 'saim.shahzad.sybrid', 'Saim#Shahzad##sybrid#2025', 'saim.shahzad@sybrid.co'],
    ['Shama', 'Younas', 'shama.younas.sybrid', 'Shama#Younas##sybrid#2025', 'shama.younas@sybrid.co'],
    ['Momna', 'Ahmad', 'momna.ahmad.sybrid', 'Momna#Ahmad##sybrid#2025', 'momna.ahmad@sybrid.co'],
    ['Ume', 'Habiba', 'ume.habiba.sybrid', 'Ume#Habiba##sybrid#2025', 'ume.habiba@sybrid.co'],
    ['Timothias', 'Bhatti', 'timothias.bhatti.sybrid', 'Timothias#Bhatti##sybrid#2025', 'timothias.bhatti@sybrid.co'],
    ['Saim', 'Talat', 'saim.talat.sybrid', 'Saim#Talat##sybrid#2025', 'saim.talat@sybrid.co'],
    ['Madiha', 'Khan', 'madiha.khan.sybrid', 'Madiha#Khan##sybrid#2025', 'madiha.khan@sybrid.co'],
    ['Haiqa', 'Naseem', 'haiqa.naseem.sybrid', 'Haiqa#Naseem##sybrid#2025', 'haiqa.naseem@sybrid.co'],
    ['Bisma', 'Gulfam', 'bisma.gulfam.sybrid', 'Bisma#Gulfam##sybrid#2025', 'bisma.gulfam@sybrid.co'],
    ['Tasia', 'Latif', 'tasia.latif.sybrid', 'Tasia#Latif##sybrid#2025', 'tasia.latif@sybrid.co'],
    ['Zain', 'Khan', 'zain.khan.sybrid', 'Zain#Khan##sybrid#2025', 'zain.khan@sybrid.co'],
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
