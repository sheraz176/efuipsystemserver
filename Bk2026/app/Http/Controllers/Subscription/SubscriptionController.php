<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;

class SubscriptionController extends Controller
{
    public static function saveSubscriptionData($msisdn,$amount,$transactionId,$referenceId, $duration,$agent_id, $planID,$product_id,$resultCode,$resultDesc,$failedReason,$Beneficinary_name,$company_id)
    {

        date_default_timezone_set('Asia/Karachi');
        //Grace Period
        $grace_period='14';

        $current_time = time(); // Get the current Unix timestamp
        $future_time = strtotime('+14 days', $current_time); // Add 14 days to the current time

        $activation_time=date('Y-m-d H:i:s');
        // Format the future time if needed
        $grace_period_time = date('Y-m-d H:i:s', $future_time);


        //Recusive Charging Date

        $future_time_recursive = strtotime("+" . $duration . " days", $current_time);
        $future_time_recursive_formatted = date('Y-m-d H:i:s', $future_time_recursive);


        // Use the Subscription model to save data to the subscription_data table
        CustomerSubscription::create([
            'customer_id'=> 1,
            'payer_cnic' => 1,
            'payer_msisdn' => $msisdn,
            'subscriber_cnic' =>1,
            'subscriber_msisdn' =>$msisdn,
            'beneficiary_name' =>$Beneficinary_name,
            'beneficiary_msisdn' =>1,
            'transaction_amount' =>$amount,
            'transaction_status' =>1,
            'referenceId' =>$referenceId,
            'cps_transaction_id' =>$transactionId,
            'cps_response_text' =>"Service Activated Sucessfully",
            'product_duration' =>$duration,
            'plan_id' =>$planID,
            'productId' =>$product_id,
            'policy_status' =>1,
            'pulse' =>"Recusive Charging",
            'api_source' =>1,
            'recursive_charging_date' => $future_time_recursive_formatted,
            'subscription_time' =>$activation_time,
            'grace_period_time' => $grace_period_time,
            'sales_agent' => $agent_id,
            'company_id' =>$company_id
        ]);
    }



    public function ivr_subscription(Request $request)
    {

        $subscriber_msisdn = $request->input("subscriber_msisdn");
        date_default_timezone_set('Asia/Karachi');
        //Grace Period
        $grace_period='14';

        $current_time = time(); // Get the current Unix timestamp
        $future_time = strtotime('+14 days', $current_time); // Add 14 days to the current time

        $activation_time=date('Y-m-d H:i:s');
        // Format the future time if needed
        $grace_period_time = date('Y-m-d H:i:s', $future_time);


        //Recusive Charging Date

        $future_time_recursive = strtotime("+" . $duration . " days", $current_time);
        $future_time_recursive_formatted = date('Y-m-d H:i:s', $future_time_recursive);

        $CustomerSubscriptionData = CustomerSubscriptionModel::create([
            'customer_id'=> -1,
            'payer_cnic' => -1,
            'payer_msisdn' => $subscriber_msisdn,
            'subscriber_cnic' =>-1,
            'subscriber_msisdn' =>$subscriber_msisdn,
            'beneficiary_name' =>-1,
            'beneficiary_msisdn' =>-1,
            'transaction_amount' =>4,
            'transaction_status' =>1,
            'referenceId' =>$referenceId,
            'cps_transaction_id' =>$transactionId,
            'cps_response_text' =>1,
            'product_duration' =>1,
            'plan_id' =>1,
            'productId' =>3,
            'policy_status' =>1,
            'pulse' =>"Recusive Charging",
            'api_source' => 2,
            'recursive_charging_date' => $future_time_recursive_formatted,
            'subscription_time' =>$activation_time,
            'grace_period_time' => $grace_period_time,
            'sales_agent' => -1
        ]);

        $CustomerSubscriptionDataID=$CustomerSubscriptionData->id;
        return response()->json(['code'=>2003,'policy_subscription_id'=>$CustomerSubscriptionDataID,'message'=>'Customer Subscribed Sucessfully','Status Code'=>200]);


    }

    public function checkSubscription(Request $request)
    {

        //  dd($request->all());
         $msisdn = $request->input('msisdn');
         $planid = $request->input('planid');

        // // Retrieve records with Plan ID 1 for the given msisdn
        $SubscriptionData = CustomerSubscription::where('subscriber_msisdn', $msisdn)->where('plan_id', $planid)->where('policy_status', 1)->first();

        //  dd($SubscriptionData);
        // // Return the data as JSON to the frontend
        if ($SubscriptionData) {
            return response()->json(['success' => true]); // Record exists
        } else {
            return response()->json(['success' => false]); // Record does not exist
        }
    }


}
