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
    ['Maryam', 'Nadeem', 'Maryam.Nadeem.tsm.2026', 'Maryam#Nadeem#tsm#2026', 'HD1697'],
    ['Danish', 'Afzal', 'Danish.Afzal.tsm.2026', 'Danish#Afzal#tsm#2026', 'HD1698'],
    ['Muhammad Abubakar', 'Tariq', 'MuhammadAbubakar.Tariq.tsm.2026', 'Muhammad Abubakar#Tariq#tsm#2026', 'HD1699'],
    ['Rabeea', 'Javed', 'Rabeea.Javed.tsm.2026', 'Rabeea#Javed#tsm#2026', 'HD1700'],
    ['Sakeena', 'Shahzadi', 'Sakeena.Shahzadi.tsm.2026', 'Sakeena#Shahzadi#tsm#2026', 'HD1701'],
    ['Hamid', 'Liaqat', 'Hamid.Liaqat.tsm.2026', 'Hamid#Liaqat#tsm#2026', 'HD1702'],
    ['Asna', 'Tariq', 'Asna.Tariq.tsm.2026', 'Asna#Tariq#tsm#2026', 'HD1703'],
    ['Muhammad', 'Ahmad', 'Muhammad.Ahmad.tsm.2026', 'Muhammad#Ahmad#tsm#2026', 'HD1704'],
    ['Areeba', 'Amanat', 'Areeba.Amanat.tsm.2026', 'Areeba#Amanat#tsm#2026', 'HD1705'],
    ['Afnan', 'Pervaiz', 'Afnan.Pervaiz.tsm.2026', 'Afnan#Pervaiz#tsm#2026', 'HD1706'],
    ['Huma', 'Khadim', 'Huma.Khadim.tsm.2026', 'Huma#Khadim#tsm#2026', 'HD1707'],
    ['Umaima', 'Urooj', 'Umaima.Urooj.tsm.2026', 'Umaima#Urooj#tsm#2026', 'HD1708'],
    ['Roshan', 'Haider', 'Roshan.Haider.tsm.2026', 'Roshan#Haider#tsm#2026', 'HD1709'],
    ['Malaika', 'Khaliq', 'Malaika.Khaliq.tsm.2026', 'Malaika#Khaliq#tsm#2026', 'HD1710'],
    ['Naila', 'Tabassum', 'Naila.Tabassum.tsm.2026', 'Naila#Tabassum#tsm#2026', 'HD1711'],
    ['Syed Ali', 'Husnain Shah', 'SyedAli.HusnainShah.tsm.2026', 'Syed Ali#Husnain Shah#tsm#2026', 'HD1712'],
    ['Aliza', 'Nadeem', 'Aliza.Nadeem.tsm.2026', 'Aliza#Nadeem#tsm#2026', 'HD1713'],
    ['Arifa', 'Batool', 'Arifa.Batool.tsm.2026', 'Arifa#Batool#tsm#2026', 'HD1714'],
    ['Ahmad', 'Ali', 'Ahmad.Ali.tsm.2026', 'Ahmad#Ali#tsm#2026', 'HD1715'],
    ['Fatima-U-Zohra', 'Zohra', 'FatimaUZohra.Zohra.tsm.2026', 'Fatima-U-Zohra#Zohra#tsm#2026', 'HD1716'],
    ['Nouman', 'Ali', 'Nouman.Ali.tsm.2026', 'Nouman#Ali#tsm#2026', 'HD1717'],
    ['Sumbal', 'Sarwar', 'Sumbal.Sarwar.tsm.2026', 'Sumbal#Sarwar#tsm#2026', 'HD1718'],
    ['Humaira', 'Ata', 'Humaira.Ata.tsm.2026', 'Humaira#Ata#tsm#2026', 'HD1719'],
    ['Wasif', 'Ali', 'Wasif.Ali.tsm.2026', 'Wasif#Ali#tsm#2026', 'HD1720'],
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
