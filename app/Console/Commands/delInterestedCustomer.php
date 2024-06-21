<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InterestedCustomers\InterestedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class delInterestedCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'del:interstadCustomer';

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
        DB::enableQueryLog();
        $InterestedCustomers = InterestedCustomer::whereIn('customer_msisdn', [
'03046707401',
'03046352210',
'03045823744',
'03045035519',
'03044793747',
'03042208498',
'03042270965',
'03041189834',
'03041042772',
'03041026207',
'03040505751',
'03040467737',
'03039861477',
'03039893409',
'03039754330',
'03039535390',
'03039486566',
'03294151101',
'03286164929',
'03286216524',
'03278738676',
'03248551766',
'03225507674',
'03218892561',
'03219763162',
'03074112398',
'03071634754',
'03067685067',
'03066250003',
'03064515603',
'03062116668',
'03046210433',
'03297255096',
'03296260912',
'03294514518',
'03296783115',
'03289631401',
'03291122279',
'03289034513',
'03294327226',
'03288933496',
'03287346653',
'03287783352',
'03286921940',
'03288974380',
'03284997626',
'03284864686',
'03284035806',
'03284125623',
'03284900418',
'03283617900',
'03282861306',
'03282750292',
'03281802568',
'03281498032',
'03281258233',
'03280815807',
'03280392770',
'03280105282',
'03280125317',
'03280411507',
'03279291705',
'03280119864',
'03278586402',
'03277716200',
'03280961172',
'03275647782',
'03280359874',
'03276187483',
'03274125430',
'03275259195',
'03273008585',
'03274839970',
'03274685644',
'03274216193',
'03271505183',
'03271506344',
'03271879836',
'03270275721',
'03269508643',
'03263366885',
'03263463696',
'03258856067',
'03257530322',
'03256776975',
'03253469850',
'03283113251',
'03252356100',
'03246862203',
'03248098630',
'03243605044',
'03241661476',
'03242827363',
'03243146584',
'03241879373',
'03240772401',
'03241686770'])->where('deduction_applied',0)->whereDate('created_at', Carbon::today())->get();
        // dd(DB::getQueryLog());
        //   dd($InterestedCustomers);


        foreach($InterestedCustomers  as $InterestedCustomer){
            // dd($subscription);
             $find_ref = InterestedCustomer::where('id',$InterestedCustomer->id)->get();
             $find_ref->each->delete();



        }


    //  dd($subscriptions);
        return 'success';
        return 0;

    }
}
