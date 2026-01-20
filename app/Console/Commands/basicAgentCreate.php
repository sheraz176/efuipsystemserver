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
    ['Sidrashaa', 'sybrideNew20211', 'sidratwosybrid2026', 'SidraTwosybrid#2026', 'wor2021gmail.com'],
    ['Momin', 'Khan', 'momin.khan.sybrid', 'Momin#Khan##sybrid#2026', 'momin.pk.mk@gmail.com'],
    ['Daniyal', 'Khattak', 'daniyal.khattak.sybrid', 'Daniyal#Khattak##sybrid#2026', 'dkhattak002@gmail.com'],
    ['Hamza', 'Akeel', 'hamza.akeel.sybrid', 'Hamza#Akeel##sybrid#2026', 'hamzaabbasiyu03@gmail.com'],
    ['Abdul', 'Raffay', 'abdul.raffay.sybrid', 'Abdul#Raffay##sybrid#2026', 'rafaylion1212@gmail.com'],
    ['Uzair', 'Gulfaraz', 'uzair.gulfaraz.sybrid', 'Uzair#Gulfaraz##sybrid#2026', 'uzairgulfarazdec2034@gmail.com'],
    ['Muhamma', 'Haris', 'muhamma.haris.sybrid', 'Muhamma#Haris##sybrid#2026', 'hariszaheer928@gamil.com'],
    ['Syed', 'YasirShah', 'syed.yasirshah.sybrid', 'Syed#YasirShah##sybrid#2026', 'syedyasirshah213141@gmail.com'],
    ['Victoria', 'Gulraiz', 'victoria.gulraiz.sybrid', 'Victoria#Gulraiz##sybrid#2026', 'jevenbhatti3@gmail.com'],
    ['Roha', 'Two', 'roha.two.sybrid', 'Roha#Two##sybrid#2026', 'rohakhokhar4@gmail.com'],
    ['Rabeeca', 'Faraz', 'rabeeca.faraz.sybrid', 'Rabeeca#Faraz##sybrid#2026', 'rabigullfaraz@gmail.com'],
    ['Junaid', 'Ahmed', 'junaid.ahmed.sybrid', 'Junaid#Ahmed##sybrid#2026', 'saggiterious666@gmail.com'],
    ['Syed', 'SaqlainAliGardazi', 'syed.saqlainaligardazi.sybrid', 'Syed#SaqlainAliGardazi##sybrid#2026', 'aligardazi2@gmail.com'],
    ['Muskan', 'Khan', 'muskan.khan.sybrid', 'Muskan#Khan##sybrid#2026', 'muskxnkhan@gmail.com'],
    ['Abubakar', 'Abbasi', 'abubakar.abbasi.sybrid', 'Abubakar#Abbasi##sybrid#2026', 'abubakerabbassi82@gmail.com'],
    ['Mamoon', 'Abbas', 'mamoon.abbas.sybrid', 'Mamoon#Abbas##sybrid#2026', 'malikmamoon12345678910@gmail.com'],
    ['Syed', 'HassanAliShah', 'syed.hassanalishah.sybrid', 'Syed#HassanAliShah##sybrid#2026', 'hs0637421@gmail.com'],
    ['Muhammad', 'Hanzala', 'muhammad.hanzala.sybrid', 'Muhammad#Hanzala##sybrid#2026', 'mhammadhanzalaqwe@gmail.com'],
    ['Muhammad', 'Ahsan', 'muhammad.ahsan.sybrid', 'Muhammad#Ahsan##sybrid#2026', 'khauuersardar15@gmail.com'],
    ['Muhammad', 'JawadAhmad', 'muhammad.jawadahmad.sybrid', 'Muhammad#JawadAhmad##sybrid#2026', 'mjawadahmad513@gmail.com'],
    ['Khawar', 'AliRiaz', 'khawar.aliriaz.sybrid', 'Khawar#AliRiaz##sybrid#2026', 'khauueraliriaz@gmail.com'],
    ['Muhammad', 'HaroonKhan', 'muhammad.haroonkhan.sybrid', 'Muhammad#HaroonKhan##sybrid#2026', 'rajaharoon1786@gmail.com'],
    ['Mehak', 'Maqsood', 'mehak.maqsood.sybrid', 'Mehak#Maqsood##sybrid#2026', 'sheikhmehak575@gmail.com'],
    ['Hasnain', 'Waheed', 'hasnain.waheed.sybrid', 'Hasnain#Waheed##sybrid#2026', 'husnainwaheed272@gmail.com'],
    ['Muhammad', 'JunaidAli', 'muhammad.junaidali.sybrid', 'Muhammad#JunaidAli##sybrid#2026', 'junaidalix244@gmail.com'],
    ['Muhammad', 'Qais', 'muhammad.qais.sybrid', 'Muhammad#Qais##sybrid#2026', 'mqais5067@gmail.com'],
    ['Aroosa', 'Khatoon', 'aroosa.khatoon.sybrid', 'Aroosa#Khatoon##sybrid#2026', 'aroosaabbasi66@gmail.com'],
    ['Nimra', 'Fatima', 'nimra.fatima.sybrid', 'Nimra#Fatima##sybrid#2026', 'abbassafdar98@gmail.com'],
    ['Sana', 'Mansha', 'sana.mansha.sybrid', 'Sana#Mansha##sybrid#2026', 'sanamansha32@gmail.com'],
    ['Noor', 'UlAin', 'noor.ulain.sybrid', 'Noor#UlAin##sybrid#2026', 'shabbirahmedcom103@gmail.com'],
    ['Usman', 'Ahmed', 'usman.ahmed.sybrid', 'Usman#Ahmed##sybrid#2026', 'usman1111256@gmail.com'],
    ['Shifa', 'Imran', 'shifa.imran.sybrid', 'Shifa#Imran##sybrid#2026', 'shifaimran796456@gmail.com'],
    ['Mudassar', 'Hussain', 'mudassar.hussain.sybrid', 'Mudassar#Hussain##sybrid#2026', 'mudassargill78687@gmail.com'],
    ['Muhammad', 'SaadGhilzai', 'muhammad.saadghilzai.sybrid', 'Muhammad#SaadGhilzai##sybrid#2026', 'saadghilzai8@gmail.com'],
    ['Abdullah', 'Mubashar', 'abdullah.mubashar.sybrid', 'Abdullah#Mubashar##sybrid#2026', 'abdullahkhan98756@gmail.com'],
    ['M', 'Arslan', 'm.arslan.sybrid', 'M#Arslan##sybrid#2026', 'arslannnmughal213@gmail.com'],
    ['Muneeba', 'Two', 'muneeba.two.sybrid', 'Muneeba#Two##sybrid#2026', 'muneebabilal143@gmail.com'],
    ['Hina', 'Haneef', 'hina.haneef.sybrid', 'Hina#Haneef##sybrid#2026', 'hinahaneef@gmail.com'],
    ['M', 'Ibrahim', 'm.ibrahim.sybrid', 'M#Ibrahim##sybrid#2026', 'Ibrahimmalik00586@gmail.com'],
    ['Mustansar', 'Abbas', 'mustansar.abbas.sybrid', 'Mustansar#Abbas##sybrid#2026', 'mustansaraliawan6@gmail.com'],
    ['Amir', 'Ali', 'amir.ali.sybrid', 'Amir#Ali##sybrid#2026', 'amiralicrick786@gmail.com'],
    ['Abdul', 'Rehman', 'abdul.rehman.sybrid', 'Abdul#Rehman##sybrid#2026', 'rehmanabdul4208@gmail.com'],
    ['Du', 'ENajaf', 'du.enajaf.sybrid', 'Du#ENajaf##sybrid#2026', 'd4durenajaf@gmail.com'],
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
