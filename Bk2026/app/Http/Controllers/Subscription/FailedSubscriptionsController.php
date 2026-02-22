<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\FailedSubscription;

class FailedSubscriptionsController extends Controller
{
    public static function saveFailedTransactionData($transactionId,$resultCode,$resultDesc,$failedReason,$amount,$referenceId,$accountNumber,$planID,$product_id,$agent_id,$company_id)
    {
        // Combine the additional fields with the existing $data
        
        // Use the Subscription model to save data to the subscription_data table
        FailedSubscription::create([
            'transactionId'=>$transactionId,
            'timeStamp'=>date('Y-m-d H:i:s'),
            'resultCode'=>$resultCode,
            'resultDesc'=>$resultDesc,
            'failedReason'=>$failedReason,
            'amount'=>$amount,
            'referenceId'=>$referenceId,
            'accountNumber'=>$accountNumber,
            'type'=>1,
            'remark'=>1,
            'planId'=>$planID,
            'product_id'=>$product_id,
            'agent_id' =>$agent_id,
            'sale_request_time' => date('Y-m-d H:i:s'),  
            'company_id'=>$company_id, 
            'source'=>'portal' 
        ]);
    }

    public static function saveFailedTransactionDataautoDebit($transactionId,$resultCode,$resultDesc,$failedReason,$amount,$referenceId,$accountNumber,$planID,$product_id,$agent_id,$company_id)
    {
        // Combine the additional fields with the existing $data
        
        // Use the Subscription model to save data to the subscription_data table
        FailedSubscription::create([
            'transactionId'=>$transactionId,
            'timeStamp'=>date('Y-m-d H:i:s'),
            'resultCode'=>$resultCode,
            'resultDesc'=>$resultDesc,
            'failedReason'=>$failedReason,
            'amount'=>$amount,
            'referenceId'=>$referenceId,
            'accountNumber'=>$accountNumber,
            'type'=>1,
            'remark'=>1,
            'planId'=>$planID,
            'product_id'=>$product_id,
            'agent_id' =>$agent_id,
            'sale_request_time' => date('Y-m-d H:i:s'),  
            'company_id'=>$company_id, 
            'source'=>'AutoDebit' 
        ]);
    }
    public static function saveFailedTransactionDataMobile($transactionId,$resultCode,$resultDesc,$failedReason,$amount,$referenceId,$accountNumber,$planID,$product_id,$agent_id,$company_id)
    {
        // Combine the additional fields with the existing $data
        
        // Use the Subscription model to save data to the subscription_data table
        FailedSubscription::create([
            'transactionId'=>$transactionId,
            'timeStamp'=>date('Y-m-d H:i:s'),
            'resultCode'=>$resultCode,
            'resultDesc'=>$resultDesc,
            'failedReason'=>$failedReason,
            'amount'=>$amount,
            'referenceId'=>$referenceId,
            'accountNumber'=>$accountNumber,
            'type'=>1,
            'remark'=>1,
            'planId'=>$planID,
            'product_id'=>$product_id,
            'agent_id' =>$agent_id,
            'sale_request_time' => date('Y-m-d H:i:s'),  
            'company_id'=>$company_id, 
            'source'=>'MobileApp' 
        ]);
    }

    public static function saveFailedTransactionLandingPage($transactionId,$resultCode,$resultDesc,$failedReason,$amount,$referenceId,$accountNumber,$planID,$product_id,$agent_id,$company_id)
    {
        // Combine the additional fields with the existing $data
        
        // Use the Subscription model to save data to the subscription_data table
        FailedSubscription::create([
            'transactionId'=>$transactionId,
            'timeStamp'=>date('Y-m-d H:i:s'),
            'resultCode'=>$resultCode,
            'resultDesc'=>$resultDesc,
            'failedReason'=>$failedReason,
            'amount'=>$amount,
            'referenceId'=>$referenceId,
            'accountNumber'=>$accountNumber,
            'type'=>1,
            'remark'=>1,
            'planId'=>$planID,
            'product_id'=>$product_id,
            'agent_id' =>$agent_id,
            'sale_request_time' => date('Y-m-d H:i:s'),  
            'company_id'=>$company_id, 
            'source'=>'LandingPage' 
        ]);
    }

}
