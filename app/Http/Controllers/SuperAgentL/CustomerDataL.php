<?php

namespace App\Http\Controllers\SuperAgentL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\TeleSalesAgent;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\Company\CompanyProfile;
use Carbon\Carbon;

class CustomerDataL extends Controller
{
    public function showForm()
    {
        $interestedcustomer = InterestedCustomer::get();
        return view('super_agent_l.customer_form',compact('interestedcustomer'));
    }

    // public function fetchCustomerData(Request $request)
    // {
    //     $customer = InterestedCustomer::where('customer_msisdn', $request->customer_msisdn)->first();

    //     return response()->json($customer);
    // }

    public function fetchCustomerData(Request $request)
    {
        // dd($request->all());
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

        return response()->json($customer);
    }
}

