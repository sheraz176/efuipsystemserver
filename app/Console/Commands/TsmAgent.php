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
    ['Samia', 'Asif', 'Samia.Asif.tsm.2025', 'Samia#Asif#tsm#2025', 'HD-1368'],
    ['Mehwish', 'Shafie', 'Mehwish.Shafie.tsm.2025', 'Mehwish#Shafie#tsm#2025', 'HD-1369'],
    ['Faizan', 'Baig', 'Faizan.Baig.tsm.2025', 'Faizan#Baig#tsm#2025', 'HD-1370'],
    ['Iqra', 'Naeem', 'Iqra.Naeem.tsm.2025', 'Iqra#Naeem#tsm#2025', 'HD-1371'],
    ['Nabiha', 'Arooj', 'Nabiha.Arooj.tsm.2025', 'Nabiha#Arooj#tsm#2025', 'HD-1372'],
    ['Hamda', 'Bashir', 'Hamda.Bashir.tsm.2025', 'Hamda#Bashir#tsm#2025', 'HD-1373'],
    ['Muhammad', 'MuhammadIqbal', 'Muhammad.MuhammadIqbal.tsm.2025', 'Muhammad#Muhammad Iqbal#tsm#2025', 'HD-1374'],
    ['Bisma', 'Kaynaat', 'Bisma.Kaynaat.tsm.2025', 'Bisma#Kaynaat#tsm#2025', 'HD-1375'],
    ['Aqleema', 'Nawaz', 'Aqleema.Nawaz.tsm.2025', 'Aqleema#Nawaz#tsm#2025', 'HD-1376'],
    ['Usama', 'Haider', 'Usama.Haider.tsm.2025', 'Usama#Haider#tsm#2025', 'HD-1377'],
    ['Muhammad', 'Talha', 'Muhammad.Talha.tsm.2025', 'Muhammad#Talha#tsm#2025', 'HD-1378'],
    ['Roman', 'Ilyas', 'Roman.Ilyas.tsm.2025', 'Roman#Ilyas#tsm#2025', 'HD-1379'],
    ['Ali', 'Raza', 'Ali.Raza.tsm.2025', 'Ali#Raza#tsm#2025', 'HD-1380'],
    ['Ghulam', 'Muhammad', 'Ghulam.Muhammad.tsm.2025', 'Ghulam#Muhammad#tsm#2025', 'HD-1381'],
    ['Karamat', 'Ali', 'Karamat.Ali.tsm.2025', 'Karamat#Ali#tsm#2025', 'HD-1382'],
    ['Wajiha', 'Tariq', 'Wajiha.Tariq.tsm.2025', 'Wajiha#Tariq#tsm#2025', 'HD-1383'],
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
