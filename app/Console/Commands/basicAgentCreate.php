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
      ['Shehrayar', 'Nazir', 'shehrayar.nazir.sybrid', 'Shehrayar#Nazir##sybrid#2026', 'Shaheryarnazir786@gmail.com'],
    ['Shazia', 'Anwar', 'shazia.anwar.sybrid', 'Shazia#Anwar##sybrid#2026', 'shaziaanwar586@gmail.com'],
    ['Muhammad', 'Farhan', 'muhammad.farhan.sybrid', 'Muhammad#Farhan##sybrid#2026', 'juttf6726@gmail.com'],
    ['Muhammad', 'Ayoub', 'muhammad.ayoub.sybrid', 'Muhammad#Ayoub##sybrid#2026', 'ayoubsethar746@gmail.com'],
    ['Muhammad', 'FarhanYounas', 'muhammad.farhanyounas.sybrid', 'Muhammad#FarhanYounas##sybrid#2026', 'itxladdi101@gmail.com'],
    ['Almas', 'Saeed', 'almas.saeed.sybrid', 'Almas#Saeed##sybrid#2026', 'saeedalmas904@gmail.com'],
    ['Shahzaib', 'Awan', 'shahzaib.awan.sybrid', 'Shahzaib#Awan##sybrid#2026', 'shahzain303sa@gmail.com'],
    ['Aradhna', 'Pervaiz', 'aradhna.pervaiz.sybrid', 'Aradhna#Pervaiz##sybrid#2026', 'aradhna.pervaiz11@gmail.com'],
    ['Sheeza', 'Ishtiaq', 'sheeza.ishtiaq.sybrid', 'Sheeza#Ishtiaq##sybrid#2026', 'ishtiaqjames437@gmail.com'],
    ['Ambreen', 'Ashfaq', 'ambreen.ashfaq.sybrid', 'Ambreen#Ashfaq##sybrid#2026', 'ambreenishfaq195@gmail.com'],
    ['Chaudhry', 'Wajahat', 'chaudhry.wajahat.sybrid', 'Chaudhry#Wajahat##sybrid#2026', 'chwajahat13@gmail.com'],
    ['Farah', 'Hameed', 'farah.hameed.sybrid', 'Farah#Hameed##sybrid#2026', 'farahhameed670@gmail.com'],
    ['Sumaira', 'Yasmeen', 'sumaira.yasmeen.sybrid', 'Sumaira#Yasmeen##sybrid#2026', 'yasmeensumaria332@gmail.com'],
    ['Saad', 'Abbasi', 'saad.abbasi.sybrid', 'Saad#Abbasi##sybrid#2026', 'saadaliabbasi625@gmail.com'],
    ['Shahbaz', 'Malik', 'shahbaz.malik.sybrid', 'Shahbaz#Malik##sybrid#2026', 'shahbazawan956@gmail.com'],
    ['Mohib', 'Ali', 'mohib.ali.sybrid', 'Mohib#Ali##sybrid#2026', 'mohibrajpoot@gmail.com'],
    ['Musab', 'Sultan', 'musab.sultan.sybrid', 'Musab#Sultan##sybrid#2026', 'mussababbasi465@gmail.com'],
    ['Nouman', 'Butt', 'nouman.butt.sybrid', 'Nouman#Butt##sybrid#2026', 'buttnouman439@gmail.com'],
    ['Faryal', 'Banu', 'faryal.banu.sybrid', 'Faryal#Banu##sybrid#2026', 'farialbanu110@gmail.com'],
    ['Rizwana', 'Bibi', 'rizwana.bibi.sybrid', 'Rizwana#Bibi##sybrid#2026', 'rizwanabibi145@gmail.com'],
    ['Sidra', 'Khurram', 'sidra.khurram.sybrid', 'Sidra#Khurram##sybrid#2026', 'sidra.khurram1992@gmail.com'],
    ['Sehar', 'Rubani', 'sehar.rubani.sybrid', 'Sehar#Rubani##sybrid#2026', 'seharrubani94@gmail.com'],
    ['Zainab', 'Zameeder', 'zainab.zameeder.sybrid', 'Zainab#Zameeder##sybrid#2026', 'zani30859@gmail.com'],
    ['Maham', 'Ramzan', 'maham.ramzan.sybrid', 'Maham#Ramzan##sybrid#2026', 'mahammahar78@gmail.com'],
    ['Alida', 'Parveen', 'alida.parveen.sybrid', 'Alida#Parveen##sybrid#2026', 'alida.parveen@gmail.com'],
    ['Areej', 'Zara', 'areej.zara.sybrid', 'Areej#Zara##sybrid#2026', 'saqibareej02@gmail.com'],
    ['Wajid', 'Iqbal', 'wajid.iqbal.sybrid', 'Wajid#Iqbal##sybrid#2026', 'dk3850006@gmail.com'],
    ['Taha', 'Jamshaid', 'taha.jamshaid.sybrid', 'Taha#Jamshaid##sybrid#2026', 'tahajamshaide005@icloud.com'],
    ['Abdullah', 'Ashfaq', 'abdullah.ashfaq.sybrid', 'Abdullah#Ashfaq##sybrid#2026', 'abdullahashfaq9988@gmail.com'],
    ['Mahran', 'Ullah', 'mahran.ullah.sybrid', 'Mahran#Ullah##sybrid#2026', 'kk3094189@gmail.com'],
    ['Adnan', 'Maseed', 'adnan.maseed.sybrid', 'Adnan#Maseed##sybrid#2026', 'adnanmaseed44@gmail.com'],
    ['Muhammad', 'Arslan', 'muhammad.arslan.sybrid', 'Muhammad#Arslan##sybrid#2026', 'arslanzahid0070@gmail.com'],
    ['Razina', 'Alam', 'razina.alam.sybrid', 'Razina#Alam##sybrid#2026', 'r8077095@gmail.com'],
    ['Moshin', 'Riaz', 'moshin.riaz.sybrid', 'Moshin#Riaz##sybrid#2026', 'moshinriazmriaz@gmail.com'],
    ['Maryam', 'Tariq', 'maryam.tariq.sybrid', 'Maryam#Tariq##sybrid#2026', 'hadayata718@gmail.com'],
    ['Fazilat', 'Naz', 'fazilat.naz.sybrid', 'Fazilat#Naz##sybrid#2026', 'faziasif8@gmail.com'],
    ['Malaika', 'Tabassum', 'malaika.tabassum.sybrid', 'Malaika#Tabassum##sybrid#2026', 'htabassum183@gmail.com'],
    ['Rameen', 'Zara', 'rameen.zara.sybrid', 'Rameen#Zara##sybrid#2026', 'tariqhjab15@gmail.com'],
    ['ZainUl', 'Abideen', 'zainul.abideen.sybrid', 'ZainUl#Abideen##sybrid#2026', 'zb8956197@gmail.com'],
    ['Ali', 'Raza', 'ali.raza.sybrid', 'Ali#Raza##sybrid#2026', 'ar025012@gmail.com'],
    ['Awais', 'Khan', 'awais.khan.sybrid', 'Awais#Khan##sybrid#2026', 'Awais765554@gmail.com'],
    ['Awais', 'AshrafAbbasi', 'awais.ashrafabbasi.sybrid', 'Awais#AshrafAbbasi##sybrid#2026', 'zurainajk786@gmail.com'],
    ['Roma', 'Fatima', 'roma.fatima.sybrid', 'Roma#Fatima##sybrid#2026', 'rumijutt60@gmail.com'],
    ['Syeda', 'MasoomaZahra', 'syeda.masoomazahra.sybrid', 'Syeda#MasoomaZahra##sybrid#2026', 'syedamasoomaz58@gmail.com'],
    ['Anjum', 'Shahzad', 'anjum.shahzad.sybrid', 'Anjum#Shahzad##sybrid#2026', 'anjumabbasianjumabbasi93@gmail.com'],
    ['Yawar', 'SudaisKhan', 'yawar.sudaiskhan.sybrid', 'Yawar#SudaisKhan##sybrid#2026', 'yawarkhan5735@gmail.com'],
    ['Arooj', 'Shabbir', 'arooj.shabbir.sybrid', 'Arooj#Shabbir##sybrid#2026', 'mirabshah011@gmail.com'],
    ['Ahtasham', 'Shakil', 'ahtasham.shakil.sybrid', 'Ahtasham#Shakil##sybrid#2026', 'ahtashamshakil30@gmail.com'],
    ['Abdul', 'Fahad', 'abdul.fahad.sybrid', 'Abdul#Fahad##sybrid#2026', 'abdulfahad@10gmail.com'],
    ['Rehman', 'Ullah', 'rehman.ullah.sybrid', 'Rehman#Ullah##sybrid#2026', 'rasmikhan128@gmail.com'],
    ['Noor', 'UlHaq', 'noor.ulhaq.sybrid', 'Noor#UlHaq##sybrid#2026', 'noorg0426@gmail.com'],
    ['Umair', 'Waqar', 'umair.waqar.sybrid', 'Umair#Waqar##sybrid#2026', 'umairkhan031544@gmail.com'],
    ['Muhammad', 'Zeeshan', 'muhammad.zeeshan.sybrid', 'Muhammad#Zeeshan##sybrid#2026', 'zeeshanmmm444@gmail.com'],
    ['Ahmad', 'NajamButt', 'ahmad.najambutt.sybrid', 'Ahmad#NajamButt##sybrid#2026', 'ahmadibutt54@gmail.com'],
    ['Syed', 'FakherImamKazmi', 'syed.fakherimamkazmi.sybrid', 'Syed#FakherImamKazmi##sybrid#2026', 'fakhardeaf5@gmail.com'],
    ['Muhammad', 'Hamza', 'muhammad.hamza.sybrid', 'Muhammad#Hamza##sybrid#2026', 'mh0668612@gmail.com'],
    ['Khansa', 'IshfaqAhmed', 'khansa.ishfaqahmed.sybrid', 'Khansa#IshfaqAhmed##sybrid#2026', 'm35139847@gmail.com'],
    ['Zikria', 'Ayaz', 'zikria.ayaz.sybrid', 'Zikria#Ayaz##sybrid#2026', 'mrzikriamughal@gmail.com'],
    ['Esha', 'Naseem', 'esha.naseem.sybrid', 'Esha#Naseem##sybrid#2026', 'zoyaznkh@gmail.com'],
    ['Mahnoor', 'Hameed', 'mahnoor.hameed.sybrid', 'Mahnoor#Hameed##sybrid#2026', 'manooch842@gmail.com'],
    ['Mariyam', 'Kashif', 'mariyam.kashif.sybrid', 'Mariyam#Kashif##sybrid#2026', 'Mughalmarry732@gmail.com'],
    ['Malik', 'AllahDitta', 'malik.allahditta.sybrid', 'Malik#AllahDitta##sybrid#2026', 'alahditta84804@gmail.com'],
    ['Muneeb', 'Ilyas', 'muneeb.ilyas.sybrid', 'Muneeb#Ilyas##sybrid#2026', 'ranamuneeb904@gmail.com'],
    ['Moeez', 'Ali', 'moeez.ali.sybrid', 'Moeez#Ali##sybrid#2026', 'moeezalisatti97@gmail.com'],
    ['Ubaid', 'Ullah', 'ubaid.ullah.sybrid', 'Ubaid#Ullah##sybrid#2026', 'ubaidsom4@gmail.com'],
    ['Noman', 'Safi', 'noman.safi.sybrid', 'Noman#Safi##sybrid#2026', 'nouman426623@gmail.com'],
    ['Jamshaid', 'Khan', 'jamshaid.khan.sybrid', 'Jamshaid#Khan##sybrid#2026', 'jk1922669@gmail.com'],
    ['Inayat', 'Ullah', 'inayat.ullah.sybrid', 'Inayat#Ullah##sybrid#2026', 'inayat199090@yahoo.com'],
    ['Liaqat', 'Ali', 'liaqat.ali.sybrid', 'Liaqat#Ali##sybrid#2026', 'liaqatali5337@gmail.com'],
    ['Arooj', 'Sabir', 'arooj.sabir.sybrid', 'Arooj#Sabir##sybrid#2026', 'aroojsabir3016@gmail.com'],
    ['Saniha', 'Asif', 'saniha.asif.sybrid', 'Saniha#Asif##sybrid#2026', 'gshahzeb423@gmail.com'],
    ['Anamta', 'Sagheer', 'anamta.sagheer.sybrid', 'Anamta#Sagheer##sybrid#2026', 'anamtasagheer406@gmail.com'],
    ['Faiqa', 'AllahDitta', 'faiqa.allahditta.sybrid', 'Faiqa#AllahDitta##sybrid#2026', 'dittahhafiz@gmail.com'],
    ['Farwa', 'Nasar', 'farwa.nasar.sybrid', 'Farwa#Nasar##sybrid#2026', 'nasarfarwa76@gmail.com'],
    ['Esha', 'Muneeb', 'esha.muneeb.sybrid', 'Esha#Muneeb##sybrid#2026', 'eshamuneeb609@gmail.com'],
    ['Ahsan', 'Nazir', 'ahsan.nazir.sybrid', 'Ahsan#Nazir##sybrid#2026', 'ahsanbhatti4581348@gmail.com'],
    ['Anita', 'Atif', 'anita.atif.sybrid', 'Anita#Atif##sybrid#2026', 'daimatif17@gmail.com'],
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
