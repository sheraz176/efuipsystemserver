<?php

namespace App\Http\Controllers\SMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class  SmsDelivery extends Controller
{
    public function smsDelivery(Request $request)
{
    //  dd($request->all());
    // Retrieve data from the AJAX request
    $msisdn = $request->input('msisdn');
    $transactionId = $request->input('transactionId');
    $agentId = $request->input('agentId');
    $referenceId = $request->input('referenceId');
    $planID= $request->input('SelectedPlan');
    $amount= $request->input('amount');
    $plantext = $request->input('plantext');


    if($plantext == 'EFU Term Takaful Plus Plan'){
        $link = "https://bit.ly/439oH0L";
    }
    else{
        $link = "https://bit.ly/3KagW3u";
    }
  //  dd($plantext);
    //return $data


    $url = 'https://api.efulife.com/itssr/its_sendsms';
    $payload = [
        'MobileNo' => $msisdn,
        'sender' => 'EFU-LIFE',
        'SMS' => "Dear Customer, You have successfully subscribed {$plantext}. for Rs .{$amount}. T&Cs:{$link} ",
    ];

    $headers = [
        'Channelcode' => 'ITS',
        'Authorization' => 'Bearer XXXXAAA123BBCITSSMS',
        'Content-Type' => 'application/json',
    ];

    $response = Http::withHeaders($headers)->post($url, $payload);

    // You can handle the response as needed
    if ($response->successful()) {
        return response()->json(['message' => 'SMS sent successfully', 'data' => $response->json()]);
    } else {
        return response()->json(['message' => 'Failed to send SMS', 'error' => $response->body()], $response->status());
    }


}

}
