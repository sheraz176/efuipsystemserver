<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeleSalesAgent;
use App\Models\Company\CompanyProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class TsmAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tsm:agent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    ['Salman', 'Ahmed', 'Salman.Ahmed.tsm.2025', 'Salman#Ahmed#tsm#2025', 'HD1307'],
    ['Faiza', 'Rashid', 'Faiza.Rashid.tsm.2025', 'Faiza#Rashid#tsm#2025', 'HD1308'],
    ['Shafaq', 'Naveed', 'Shafaq.Naveed.tsm.2025', 'Shafaq#Naveed#tsm#2025', 'HD1309'],
    ['Minahil', 'Mazhar', 'Minahil.Mazhar.tsm.2025', 'Minahil#Mazhar#tsm#2025', 'HD1310'],
    ['Rachel', 'HanookGill', 'Rachel.Hanook.Gill.tsm.2025', 'Rachel#Hanook Gill#tsm#2025', 'HD1311'],
    ['Shahnaz', 'Imran', 'Shahnaz.Imran.tsm.2025', 'Shahnaz#Imran#tsm#2025', 'HD1312'],
    ['Aliza', 'Sharif', 'Aliza.Sharif.tsm.2025', 'Aliza#Sharif#tsm#2025', 'HD1313'],
    ['Abdul', 'Rehman', 'Abdul.Rehman.tsm.2025', 'Abdul#Rehman#tsm#2025', 'HD1314'],
    ['Sadiq', 'Hussain', 'Sadiq.Hussain.tsm.2025', 'Sadiq#Hussain#tsm#2025', 'HD1315'],
    ['Fardeen', 'Khan', 'Fardeen.Khan.tsm.2025', 'Fardeen#Khan#tsm#2025', 'HD1316'],
    ['Muhammad', 'Sumeel', 'Muhammad.Sumeel.tsm.2025', 'Muhammad#Sumeel#tsm#2025', 'HD1181'],
    ['Saira', 'Bano', 'Saira.Bano.tsm.2025', 'Saira#Bano#tsm#2025', 'HD678'],
    ['MUHAMMAD', 'SALMAN', 'MUHAMMAD.SALMAN.tsm.2025', 'MUHAMMAD#SALMAN#tsm#2025', 'HD296'],
    ['SANA', 'ASIF', 'SANA.ASIF.tsm.2025', 'SANA#ASIF#tsm#2025', 'HD363'],
    ['TAHIRA', 'TABASUM', 'TAHIRA.TABASUM.tsm.2025', 'TAHIRA#TABASUM#tsm#2025', 'HD719'],
    ['AROOJ', 'ASLAM', 'AROOJ.ASLAM.tsm.2025', 'AROOJ#ASLAM#tsm#2025', 'HD862'],
    ['ZOHA', 'RIAZ', 'ZOHA.RIAZ.tsm.2025', 'ZOHA#RIAZ#tsm#2025', 'HD905'],
    ['AIMA', 'IMRAN', 'AIMA.IMRAN.tsm.2025', 'AIMA#IMRAN#tsm#2025', 'HD1040'],
    ['KHADIJA', '2nd', 'KHADIJA.2nd.tsm.2025', 'KHADIJA##tsm#2025', 'HD1048'],
    ['SADIA', 'SHAHZADI', 'SADIA.SHAHZADI.tsm.2025', 'SADIA#SHAHZADI#tsm#2025', 'Hd1052'],
    ['MAMOONA', 'RAMZAN', 'MAMOONA.RAMZAN.tsm.2025', 'MAMOONA#RAMZAN#tsm#2025', 'HD1164'],
    ['MUHAMMAD', 'SAIFULLAH', 'MUHAMMAD.SAIFULLAH.tsm.2025', 'MUHAMMAD#SAIFULLAH#tsm#2025', 'HD1188'],
    ['SAHER', 'SAFARISH', 'SAHER.SAFARISH.tsm.2025', 'SAHER#SAFARISH#tsm#2025', 'HD1176'],
    ['AREEBA', 'BABAR', 'AREEBA.BABAR.tsm.2025', 'AREEBA#BABAR#tsm#2025', 'HD1196'],
    ['NUZHAT', 'NAYYAB', 'NUZHAT.NAYYAB.tsm.2025', 'NUZHAT#NAYYAB#tsm#2025', 'HD1224'],
    ['MAHNOOR', 'YASEEN', 'MAHNOOR.YASEEN.tsm.2025', 'MAHNOOR#YASEEN#tsm#2025', 'HD1237'],
];






    foreach ($agentsData as $data) {
        $request = [
            'first_name' => $data[0],
            'last_name' => $data[1],
            'username' => $data[2],
            'emp_code' => $data[4],
            'status' => 1,
            'company_id' => 11,
            'password' => $data[3],
            'email' => strtolower($data[2]) . '@gmail.com',  // Using username for the email
        ];

        // Validation
        $validator = Validator::make($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:tele_sales_agents',
            'email' => 'required|email|unique:tele_sales_agents',
            'status' => 'required|in:1,0',
            'company_id' => 'required',
            'password' => 'required|min:6',
            'emp_code' => 'required',
        ]);

        if ($validator->fails()) {
            $this->error("Validation failed for: " . $request['username']);
            continue;
        }

        // Creating the telesales agent record
        TeleSalesAgent::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'username' => $request['username'],
            'email' => $request['email'],
            'status' => $request['status'],
            'company_id' => $request['company_id'],
            'password' => Hash::make($request['password']),
            'islogin' => 0,
            'call_status' => 0,
            'today_login_time' => now(),
            'today_logout_time' => now(),
            'emp_code' => $request['emp_code'],
        ]);

        $this->info("Created Telesales Agent: " . $request['username']);
    }

    return 0;
}

}
