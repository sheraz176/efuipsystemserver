<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class BankPolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:policy';

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
        $pairs = [

            ['03058048054',2000],
            ['03097202377',1950],
            ['03099343346',1950],
            ['03278288505',2000],
            ['03246283357',2000],
            ['03272240024',1950],
            ['03292687243',1950],
            ['03001333123',1950],
            ['03008908703',1950],
            ['03215091786',2000],
            ['03283475109',1950],
            ['03354250157',2000],
            ['03258695173',2000],
            ['03281484878',2000],
            ['03060516619',1950],
            ['03035983673',2000],
            ['03247433750',2000],
            ['03208771892',2000],
            ['03086582737',1950],
            ['03248063518',1950],
            ['03128959559',2000],
            ['03004933345',1950],
            ['03237366295',2000],
            ['03092928376',1950],
            ['03032264317',2000],
            ['03287307350',1950],
            ['03008789980',1950],
            ['03088763767',1950],
            ['03256264629',1950],
            ['03065753537',1950],
            ['03226988433',1950],
            ['03063886691',1950],
            ['03248647872',2000],
            ['03180157492',1950],
            ['03014844869',1950],
            ['03015179426',2000],
            ['03088683332',1950],
            ['03084393082',1950],
            ['03027941559',1950],
            ['03281302086',1950],
            ['03229267301',2000],
            ['03298899512',1950],
            ['03092721911',2000],
            ['03266608039',1950],
            ['03286020219',1950],
            ['03140453886',1950],
            ['03076161911',1950],
            ['03257908195',1950],
            ['03280053663',1950],
            ['03221702085',1950],
            ['03137090587',2000],
            ['03060041534',1950],
            ['03238700653',2000],
            ['03292475748',2000],
            ['03206017018',2000],
            ['03018617522',2000],
            ['03069667213',1950],
            ['03280552241',1950],
            ['03011539292',2000],
            ['03223079355',1950],
            ['03206031389',2000],
            ['03223427734',2000],
            ['03038049620',2000],
            ['03298066334',1950],
            ['03190569532',1950],
            ['03240904050',1950],
            ['03209597002',2000],
            ['03430878186',2000],
            ['03012881660',1950],
            ['03044284467',2000],
            ['03227672786',1950],
            ['03044304719',1950],
            ['03027559340',2000],
            ['03215614683',2000],
            ['03271149884',2000],
            ['03066914932',1950],
            ['03005330622',2000],
            ['03027038008',2000],
            ['03290574663',2000],
            ['03039003471',2000],
            ['03270886882',2000],
            ['03267320250',1950],
            ['03068201725',1950],
            ['03004005474',1950],
            ['03166979727',1950],
            ['03056411028',1950],
            ['03060307479',1950],
            ['03338969573',2000],
            ['03196587400',1950],
            ['03207558411',1950],
            ['03024708434',1950],
            ['03203909348',1950],
            ['03156332317',1950],
            ['03087310103',1950],
            ['03056819723',1950],
            ['03014425644',1950],
            ['03703702386',1950],
            ['03039464013',1950],
            ['03004399036',1950],
            ['03224327042',1950],
            ['03234964576',1950],
            ['03243626180',1950],
            ['03048883032',1950],
            ['03245257945',1950],
            ['03122361860',1950],
            ['03229404426',1950],
            ['03082107274',1950],
            ['03007877106',1950],
            ['03002409097',1950],
            ['03214790924',1950],
            ['03020781225',1950],
            ['03296691901',1950],
            ['03041213072',1950],
            ['03281122778',2000],
            ['03091375638',1950],
            ['03034423151',1950],
            ['03073185896',1950],
            ['03218614208',2000],
            ['03063694015',2000],
            ['03237937343',2000],
            ['03291686762',2000],
            ['03004284348',2000],
            ['03054442484',2000],
            ['03244848224',2000],
            ['03078300131',2000],
            ['03298127746',2000],
            ['03262535680',2000],
            ['03241610310',1950],
            ['03217499021',2000],
            ['03411375380',2000],
            ['03707476092',2000],
            ['03009407287',2000],
            ['03287242918',2000],
            ['03017454672',2000],
            ['03485389998',2000],
            ['03046955880',2000],
            ['03097085616',2000],
            ['03007435986',2000],
            ['03036580088',2000],
            ['03135532380',2000],

            // ...
        ];

        $subscriptions = CustomerSubscription::where(function ($query) use ($pairs) {
            foreach ($pairs as $pair) {
                $query->orWhere(function ($subQuery) use ($pair) {
                    $subQuery->where('subscriber_msisdn', $pair[0])
                        ->where('transaction_amount', $pair[1]);
                });
            }
        })->get();

        dd($subscriptions);


        foreach ($subscriptions as $subscription) {


            dd($subscription);


            $find_sub = CustomerSubscription::find($subscription->subscription_id);

               $find_sub->policy_status = '0';
                $find_sub->update();

                Log::channel('unsub_number_log')->info('Bank Refund Msisdn.', [
                    'Sub-Id' => $subscription->subscription_id,
                    'Redund-Msisdn-number' => $subscription->subscriber_msisdn,
                    'transaction_amount' => $subscription->transaction_amount,

                ]);

             $refundedCustomer = RefundedCustomer::create([
                 'subscription_id' => $subscription->subscription_id,
                 'unsubscription_id' => 2,
                 'transaction_id' => $subscription->cps_transaction_id,
                 'reference_id' => $subscription->referenceId,
                 'cps_response' => $subscription->cps_response_text,
                 'result_description' => $subscription->cps_response_text,
                 'result_code' => 0,
                 'refunded_by' => 'Danish2024',
                 'medium' => 'Bank Refund',
             ]);


             $CustomerUnSub = CustomerUnSubscription::create([
                'unsubscription_datetime' => now(),
                'medium' => "Bank Refund",
                'subscription_id' => $subscription->subscription_id,
                 'refunded_id' => $refundedCustomer->refund_id,
             ]);
        }


        //  dd($subscriptions);
        return 'success';

    }
}
