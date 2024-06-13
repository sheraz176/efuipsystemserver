<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class deleteSub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sub:delete';

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
        $subscriptions = CustomerSubscription::whereIn('subscriber_msisdn', ['03005242306',
'03014667548',
'03211404528',
'03064063494',
'03077080303',
'03064244734',
'03091539812',
'03097717448',
'03440018678',
'03054866409',
'03064417357',
'03219828878',
'03286566357',
'03042043530',
'03000430512',
'03424731514',
'03017982929',
'03294091566',
'03029071623',
'03247794270',
'03234064711',
'03137545320',
'03217072887',
'03032931580',
'03214185914',
'03206483095',
'03064736838',
'03324695140',
'03226508016',
'03054834010',
'03299825003',
'03078371575',
'03060693136',
'03040686581',
'03055619020',
'03264707798',
'03074562385',
'03088905904',
'03004694440',
'03044314642',
'03047676042',
'03081683042',
'03086958902',
'03057989946',
'03249745991',
'03002573749',
'03066813159',
'03025501483',
'03068413449',
'03042068438',
'03074009649',
'03206858954',
'03231034011',
'03035164500',
'03153206182',
'03064415362',
'03094971704',
'03004374864',
'03044212211',
'03024080255',
'03034472807',
'03206865923',
'03064768068',
'03254774722',
'03044053059',
'03024488054',
'03057910062',
'03064918091',
'03001314617',
'03039098380',
'03044057645',
'03204008116',
'03242337495',
'03344081070',
'03046158313',
'03024004274',
'03274567990',
'03030618113',
'03287967567',
'03026253710',
'03004706449',
'03010117685',
'03256400078',
'03134207229',
'03028405371',
'03004543182',
'03000065457',
'03005007831',
'03004212216',
'03266153728',
'03096042036',
'03061402042',
'03024345166',
'03069451187',
'03084378825',
'03078436087',
'03141435011',
'03008022364',
'03000716959',
'03127671965',
'03235086071',
'03467940971',
'03215758372',
'03046605400',
'03288014636',
'03037757660',
'03073770984',
'03245168624',
'03066650727',
'03221707047',
'03029390333',
'03034073272',
'03066259327',
'03227263003',
'03274954181',
'03454689836',
'03288022625',
'03296266727',
'03004781327',
'03283125601',
'03284536776',
'03011202916',
'03071301281',
'03099050233',
'03010070889',
'03004069349',
'03261745803',
'03283126140',
'03259889844',
'03014029475',
'03059599324',
'03099439012',
'03285596869',
'03071700439',
'03004880690',
'03046511236',
'03066648016',
'03010023636',
'03026112984',
'03078989980',
'03096850980',
'03284961640',
'03228413473',
'03275405966',
'03046679085',
'03065935368',
'03111028022',
'03223879799',
'03239633729',
'03214101971',
'03000023542',
'03086174404',
'03035730052',
'03017350980',
'03069669330',
'03227176756',
'03224115960',
'03008000190',
'03064027703',
'03067344216',
'03002057749',
'03004981897',
'03217600326',
'03054099116',
'03099731498',
'03025559159',
'03284225574',
'03285772343',
'03054037147',
'03094840021',
'03077054432',
'03285332682',
'03285772347',
'03024858049',
'03264638626',
'03054081017',
'03285757174',
'03045119692',
'03037138724',
'03054530741',
'03207701097',
'03029420385',
'03064706179',
'03201515851',
'03030047815',
'03284779529',
'03285623150',
'03007293465',
'03055418921',
'03299682843',
'03416530345',
'03074683975',
'03285757190',
'03289206552',
'03286566573',
'03024118025',
'03036265313',
'03026530904',
'03287200303',
'03431149049',
'03064686081',
'03004462030',
'03021727650',
'03297324987',
'03064028598',
'03003583317',
'03016433269',
'03454517265',
'03094002637',
'03042859099',
'03077005900',
'03207220639',
'03084798226',
'03004676439',
'03074510097',
'03260941647',
'03451380940',
'03021732120',
'03074301526',
'03239291796',
'03001234547',
'03066520400',
'03060497797',
'03259889566',
'03061849334',
'03077276065',
'03054487039',
'03079045200',
'03168592987',
'03075603341',
'03084233936',
'03086674366',
'03069694285',
'03154219162',
'03034123450',
'03047213104',
'03044696708',
'03217179556',
'03006499273',
'03017961152',
'03084595334',
'03067571816',
'03006663949',
'03057051523',
'03024989300',
'03070749500',
'03059905315',
'03266537754',
'03278164144',
'03216868668',
'03005665914',
'03248660075',
'03060005315',
'03070608704',
'03288026300',
'03049791878',
'03004145032',
'03044824440',
'03055885215',
'03009647205',
'03454000464',
'03191779696',
'03254323439',
'03077570735',
'03024545086',
'03107154213',
'03007497537',
'03034723212',
'03000959586',
'03236530218',
'03014807972',
'03024335240',
'03441261485',
'03016264720',
'03217327827',
'03465435263',
'03011270338',
'03269341821',
'03190148746',
'03032216456',
'03274833661',
'03004727320',
'03006312288',
'03008819337',
'03044470543',
'03206733747',
'03238834105',
'03278784040',
'03288959778',
'03294092329',
'03024195556',
'03225804902',
'03231699151',
'03288026309',
'03074757582',
'03246264229',
'03066700994',
'03097712977',
'03075516087',
'03201280604',
'03029614407',
'03074129140',
'03058332560',
'03044624513',
'03237969870',
'03099437881',
'03249450599',
'03209779054',
'03268853422',
'03274227255',
'03014950986',
'03084545020'])->get();
        // dd(DB::getQueryLog());
        //  dd($subscriptions);


        foreach($subscriptions as $subscription){
            // dd($subscription);
        //     $find_ref = RefundedCustomer::where('subscription_id',$subscription->subscription_id)->get();
        //     $find_ref->each->delete();
        //     $find_unsub = CustomerUnSubscription::where('subscription_id',$subscription->subscription_id)->get();
        //    $find_unsub->each->delete();
        //      $find_sub = CustomerSubscription::where('subscription_id',$subscription->subscription_id)->get();
        //      $find_sub->each->delete();


        }


    //  dd($subscriptions);
        return 'success';
        return 0;
    }
}
