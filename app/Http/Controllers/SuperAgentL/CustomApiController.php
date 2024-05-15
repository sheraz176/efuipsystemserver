<?php

namespace App\Http\Controllers\SuperAgentL;

use App\Http\Controllers\Controller;
use App\Models\InterestedCustomers\InterestedCustomer;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Carbon\Carbon;

class CustomApiController extends Controller
{

    public function status_update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'customer_msisdn' => 'required',
            'number_of_proceed' => 'required',
         ]);

       if ($validator->fails()) {
          return response()->json(['status' => 'Error','message' => $validator->errors()],200);
         }
        $customer_msisdn = $request->customer_msisdn;
        //  dd($customer_msisdn);
        $interested_customer = InterestedCustomer::where('customer_msisdn', $customer_msisdn)
        ->whereDate('created_at', Carbon::today())
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

        }

        return response()->json(['status' => 'Error','message' => 'Customer Msisdn Number is Not available.'], 200);

    }
}
