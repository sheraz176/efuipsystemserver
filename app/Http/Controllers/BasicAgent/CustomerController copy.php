<?php

namespace App\Http\Controllers\BasicAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterestedCustomers\InterestedCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
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

        $customerChecks = InterestedCustomer::where('customer_msisdn', $request->customer_msisdn)
         ->whereDate('created_at', Carbon::today())
         ->first();
         if ($customerChecks) {
            return response()->json(['success' => false, 'message' => 'Today Already Number Add Interested Customer'], 500);
        }
        // Create a new InterestedCustomer instance
        $customer = InterestedCustomer::create($validatedData);

        if ($customer) {
            // If customer created successfully, return success response
            return response()->json(['success' => true, 'message' => 'Customer saved successfully']);
        } else {
            // If customer creation failed, return error response
            return response()->json(['success' => false, 'message' => 'Failed to save customer'], 500);
        }

    }
}
