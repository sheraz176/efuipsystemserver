<?php

namespace App\Console\Commands;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdatePolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:policy';

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
        $subscriptions = CustomerSubscription::whereIn('subscriber_msisdn', [
'03245857676',
'03230329358',
'03111414556',
'03092021828',
'03026304261',
'03280590151',
'03455164041',
'03248382791',
'03052411263',
'03008065312',
'03065175565',
'03103463598',
'03035750955',
'03082277496',
'03045445445',
'03457929669',
'03286529205',
'03216480690',
'03336103898',
'03178488185',
'03269908125',
'03353259232',
'03034618535',
'03158088589',
'03261742645',
'03270106531',
'03021321900',
'03256112419',
'03078609044',
'03064482647',
'03093865137',
'03034622040'
])->get();
        // dd(DB::getQueryLog());
         dd($subscriptions);


        foreach($subscriptions as $subscription){
            // dd($subscription);
          $find_sub = CustomerSubscription::find($subscription->subscription_id);
          $find_sub->policy_status = '0';
          $find_sub->update();

          $refundedCustomer=RefundedCustomer::create([
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


          $CustomerUnSub= CustomerUnSubscription::create([
            'unsubscription_datetime' => now(),
            'medium' => "Bank Refund",
            'subscription_id' => $subscription->subscription_id,
            'refunded_id' => $refundedCustomer->refund_id,
             ]);

        }


    //  dd($subscriptions);
        return 'success';
        return 0;
    }
}
