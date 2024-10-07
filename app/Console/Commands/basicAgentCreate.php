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
            ['Muneeb', 'Mubashir', 'Muneeb.Mubashir.sybrid', 'Muneeb#Mubashir#sybrid#2024', 'muneebfarooqui31@gmail.com'],
            ['M Kamil', 'Ali', 'Kamil.Ali.sybrid', 'Kamil#Ali#sybrid#2024', 'hassankhan502133@gmail.com'],
            ['Imtiaz', 'Ahmad', 'Imtiaz.Ahmad.sybrid', 'Imtiaz#Ahmad#sybrid#2024', 'wafaimtiaz00@gmail.com'],
            ['Anwar', 'Saeed', 'Anwar.Saeed.sybrid', 'Anwar#Saeed#sybrid#2024', 'anwar0099saeed@gmail.com'],
            ['Shan e', 'Hassan', 'ShaneHassan.sybrid', 'ShaneHassan#sybrid#2024', 'shanehassan50@gmail.com'],
            ['Sardar Ali', 'Hassan', 'SardarAliHassan.sybrid', 'SardarAliHassan#sybrid#2024', 'sardaralihassanzahid01@gmail.com'],
            ['Sardar Muhammad', 'Hassan', 'SardarMHassan.sybrid', 'SardarMHassan#sybrid#2024', 'sardarmhassan2002@gmail.com'],
            ['Abdul', 'Baseer', 'AbdulBaseer.sybrid', 'AbdulBaseer#sybrid#2024', 'abdulbaseer8881@gmail.com'],
            ['Muhammad', 'Usman', 'MuhammadUsman.sybrid', 'MuhammadUsman#sybrid#2024', 'ukdesigner007@gmail.com'],
            ['Hafsa', 'Anwar', 'Hafsa.Anwar.sybrid', 'Hafsa#Anwar#sybrid#2024', 'sybfly5@gmail.com'],
            ['Muhammad Sikandar', 'Abbasi', 'SikandarAbbasi.sybrid', 'SikandarAbbasi#sybrid#2024', 'sikandarabbasi112233@gmail.com'],
            ['Sadaf', 'Mushtaq', 'SadafMushtaq.sybrid', 'SadafMushtaq#sybrid#2024', 'itxsadafrj9@gmail.com'],
            ['Esha Bashir', 'Abbasi', 'EshaBashirAbbasi.sybrid', 'EshaBashirAbbasi#sybrid#2024', 'eshaabbasi66@gmail.com'],
            ['Anam', 'Mustafa', 'AnamMustafa.sybrid', 'AnamMustafa#sybrid#2024', 'mustafaanum88@gmail.com'],
            ['Faizan', 'Khurshid', 'FaizanKhurshid.sybrid', 'FaizanKhurshid#sybrid#2024', 'meerfaizankhursheed@gmail.com'],
            ['Malaika', 'Arif', 'MalaikaArif.sybrid', 'MalaikaArif#sybrid#2024', 'malaikaarif33333@gmail.com'],
            ['Mohsin', 'Naseer', 'MohsinNaseer.sybrid', 'MohsinNaseer#sybrid#2024', 'mohsinnaseer63@gmail.com'],
            ['Abu', 'Huraira', 'AbuHuraira.sybrid', 'AbuHuraira#sybrid#2024', 'ahahxd8932@gmail.com'],
            ['Hafiz Muhammad', 'Hashir Kiani', 'HashirKiani.sybrid', 'HashirKiani#sybrid#2024', 'hashirkiani15000@gmail.com'],
            ['Umer', 'Nasir', 'UmerNasir.sybrid', 'UmerNasir#sybrid#2024', 'umernasir008@gmail.com'],
            ['Shahzad', 'Ahmed', 'ShahzadAhmed.sybrid', 'ShahzadAhmed#sybrid#2024', 'shahzadinfo1436@gmail.com'],
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
