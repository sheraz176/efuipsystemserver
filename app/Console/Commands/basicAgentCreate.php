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
            ['Ammara', 'Abbas', 'Ammara.Abbas.abacus', 'Ammara#Abbas#abacus#2025', 'Ammara.Abbas.abacus@gmail.com'],
            ['Muqaddas', 'Fayyaz', 'Muqaddas.Fayyaz.abacus', 'Muqaddas#Fayyaz#abacus#2025', 'Muqaddas.Fayyaz.abacus@gmail.com'],
            ['Anoosha', 'Irfan', 'Anoosha.Irfan.abacus', 'Anoosha#Irfan#abacus#2025', 'Anoosha.Irfan.abacus@gmail.com'],
            ['Zunaira', 'Shahzad', 'Zunaira.Shahzad.abacus', 'Zunaira#Shahzad#abacus#2025', 'Zunaira.Shahzad.abacus@gmail.com'],
            ['Aliza', 'Yousaf', 'Aliza.Yousaf.abacus', 'Aliza#Yousaf#abacus#2025', 'Aliza.Yousaf.abacus@gmail.com'],
            ['Sumbal', 'Zahoor', 'Sumbal.Zahoor.abacus', 'Sumbal#Zahoor#abacus#2025', 'Sumbal.Zahoor.abacus@gmail.com'],
            ['Kainat', 'Loan', 'Kainat.Loan.abacus', 'Kainat#Loan#abacus#2025', 'Kainat.Loan.abacus@gmail.com'],
            ['Asma', 'Shaukat', 'Asma.Shaukat.abacus', 'Asma#Shaukat#abacus#2025', 'Asma.Shaukat.abacus@gmail.com'],
            ['Alishba', 'Latif', 'Alishba.Latif.abacus', 'Alishba#Latif#abacus#2025', 'Alishba.Latif.abacus@gmail.com'],
            ['Hina', 'Parveen', 'Hina.Parveen.abacus', 'Hina#Parveen#abacus#2025', 'Hina.Parveen.abacus@gmail.com'],
            ['Maira', 'Naik Muhammad', 'Maira.Naik.Muhammad.abacus', 'Maira#Naik#Muhammad#abacus#2025', 'Maira.Naik.Muhammad.abacus@gmail.com'],
            ['Iram', 'Akram', 'Iram.Akram.abacus', 'Iram#Akram#abacus#2025', 'Iram.Akram.abacus@gmail.com']
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
