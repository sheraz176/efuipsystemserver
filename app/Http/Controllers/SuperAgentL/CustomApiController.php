<?php

namespace App\Http\Controllers\SuperAgentL;

use App\Http\Controllers\Controller;
use App\Models\InterestedCustomers\InterestedCustomer;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\AutoDebitRequest;

class CustomApiController extends Controller
{

    public function status_update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'customer_msisdn' => 'required',
            'number_of_proceed' => 'required',
         ]);

       if ($validator->fails()) {
            // Logs
            Log::channel('consent_api')->info('Consent Api Error.',[
                'Error-data' =>  $validator->errors(),
                ]);
          return response()->json(['status' => 'Error','message' => $validator->errors()],200);
         }
        $customer_msisdn = $request->customer_msisdn;


    $autoDebitRequestData = AutoDebitRequest::where('msisdn', $customer_msisdn)
    ->whereDate('created_at', Carbon::today())
    ->first();

    if (!$autoDebitRequestData) {
        return response()->json(['status' => 'Error','message' => 'Customer Msisdn Number is Not available.'], 200);

    }

    $interested_customer_id = $autoDebitRequestData->interested_customer_id;
    // Query to check if consistent_provider is 1 for the given MSISDN

        //  dd($customer_msisdn);
        $interested_customer = InterestedCustomer::where('id', $interested_customer_id)
        ->where('deduction_applied', 0)
        ->first();
        //  dd($interested_customer);
        if ($interested_customer) {
            $interested_customer->number_of_proceed = $request->number_of_proceed;
            $interested_customer->consistent_provider = "1";
            $interested_customer->update();
            $data = array(
              'status' => 'Success',
              'message' => 'Your Status Update Successfully',
              'interested_customer' => $interested_customer,
            );
            return response()->json($data ,200);

              // Logs
              Log::channel('consent_api')->info('Consent Api.',[
                'response-data' =>  $data,
                ]);

        }

        return response()->json(['status' => 'Error','message' => 'Customer Msisdn Number is Not available.'], 200);

    }
}
