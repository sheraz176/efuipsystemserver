<?php

namespace App\Http\Controllers\SuperAgentInterested;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\TeleSalesAgent;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\Company\CompanyProfile;
use App\Models\interestedCustomerData; // Ensure you import your model
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomerDataInterested extends Controller
{
    public function showForm()
    {
        return view('super_agent_Interested.customer_form');
    }

    // public function fetchCustomerData(Request $request)
    // {
    //     $customer = InterestedCustomer::where('customer_msisdn', $request->customer_msisdn)->first();

    //     return response()->json($customer);
    // }

    public function fetchCustomerData(Request $request)
    {
        $customer = InterestedCustomer::with(['agent', 'company', 'plan', 'product'])
        ->where('customer_msisdn', $request->customer_msisdn)
        ->whereDate('created_at', Carbon::today())
        ->where('deduction_applied', 0)
        ->first();

        // Check if customer exists
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        // Retrieve associated data
        $agent = TeleSalesAgent::find($customer->agent_id);
        $company = CompanyProfile::find($customer->company_id);
        $plan = PlanModel::find($customer->plan_id);
        $product = ProductModel::find($customer->product_id);

        // Append associated data to customer
        $customer->agent_name = $agent->username ?? null;
        $customer->company_name = $company->company_name ?? null;
        $customer->plan_name = $plan->plan_name ?? null;
        $customer->product_name = $product->product_name ?? null;

        $customer->agent_id = $agent->agent_id ?? null;
        $customer->company_id = $company->id ?? null;
        $customer->plan_id = $plan->plan_id  ?? null;
        $customer->product_id = $product->product_id  ?? null;
        $customer->product_amount = $product->fee ?? null;

        return response()->json($customer);
    }

    public function interestedCustomerData(Request $request)
    {
    //    dd($request->all());
        // Validate the request data
         $validator = Validator::make($request->all(), [
             'subscriber_msisdn' => 'required',
             'customer_cnic' => 'required',
             'plan_id' => 'required',
             'product_id' => 'required',
             'agent_id' => 'required',
            'company_id' => 'required'
         ]);

         if ($validator->fails()) {
             return response()->json([
                 'data' => [
                    'message' => 'Validation error',
                     'errors' => $validator->errors()
                 ]
             ], 422);
         }

         $customerChecks = interestedCustomerData::where('subscriber_msisdn', $request->subscriber_msisdn)
         ->whereDate('created_at', Carbon::today())
         ->first();

     if ($customerChecks) {
         return response()->json([
             'data' => [
                 'message' => 'Interested Customer already Registered for today!',
             ]
         ], 422); // 409 Conflict status code
     }
        //  dd($customerChecks);

        // Create a new record in the InteresedCustomerData table
        $interestedCustomerData = interestedCustomerData::create([
            'subscriber_msisdn' => $request->input('subscriber_msisdn'),
            'customer_cnic' => $request->input('customer_cnic'),
            'plan_id' => $request->input('plan_id'),
            'product_id' => $request->input('product_id'),
            'amount' => $request->input('product_amount'),
            'beneficiary_msisdn' => $request->input('beneficiary_msisdn'),
            'beneficiary_cnic' => $request->input('beneficiary_cnic'),
            'beneficiary_name' => $request->input('beneficiary_name'),
            'agent_id' => $request->input('agent_id'),
            'company_id' => $request->input('company_id'),


        ]);
        //    dd($interestedCustomerData);
        return response()->json([
            'data' => [
                'message' => 'Customer data stored successfully!',
                'customer' => $interestedCustomerData
            ]
        ], 200);

    }
}

