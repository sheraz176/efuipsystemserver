<?php

namespace App\Http\Controllers\BasicAgentL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterestedCustomers\InterestedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\AutoDebitRequest;

class CustomerBasicAgentLController extends Controller
{

    public function saveCustomer(Request $request)
{
    // dd($request->all());
    // Validate the incoming request
    $validatedData = $request->validate([
        'customer_msisdn' => 'required|string|max:255',
        'customer_cnic' => 'required|string|max:255',
        'plan_id' => 'required|integer',
        'product_id' => 'required|integer',
        'beneficiary_msisdn' => 'required|string|max:255',
        'beneficiary_cnic' => 'required|string|max:255',
        'relationship' => 'required|string|max:255',
        'beneficinary_name' => 'required|string|max:255',
        'agent_id' => 'required|integer',
        'company_id' => 'required|integer',
    ]);

    $today = Carbon::now('Asia/Karachi')->format('Y-m-d');
    $uniqueKey = $request->customer_msisdn . '_' . $today;

    try {
        // Create a new InterestedCustomer instance
        $InterestedCustomer = new InterestedCustomer();
        $InterestedCustomer->customer_msisdn = $request->customer_msisdn;
        $InterestedCustomer->customer_cnic = $request->customer_cnic;
        $InterestedCustomer->plan_id = $request->plan_id;
        $InterestedCustomer->product_id = $request->product_id;
        $InterestedCustomer->beneficiary_msisdn = $request->beneficiary_msisdn;
        $InterestedCustomer->beneficiary_cnic = $request->beneficiary_cnic;
        $InterestedCustomer->relationship = $request->relationship;
        $InterestedCustomer->beneficinary_name = $request->beneficinary_name;
        $InterestedCustomer->agent_id = $request->agent_id;
        $InterestedCustomer->company_id = $request->company_id;
        $InterestedCustomer->unique_key = $uniqueKey;
        $InterestedCustomer->save();

           // Save the MSISDN and today's date in the database
           AutoDebitRequest::create([
               'msisdn' => $request->customer_msisdn,
               'agent_id' => $request->agent_id,
           ]);

        return response()->json(['success' => true, 'message' => 'Customer saved successfully']);
    } catch (\Illuminate\Database\QueryException $e) {
        // Check if the error is due to a unique constraint violation
        if ($e->errorInfo[1] == 1062) { // 1062 is the error code for duplicate entry in MySQL
            return response()->json(['success' => false, 'message' => 'Today Already Number Add Interested Customer'], 500);
        } else {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}

}
