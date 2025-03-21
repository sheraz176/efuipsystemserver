<?php

namespace App\Http\Controllers\BasicAgentL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\TeleSalesAgent;
use App\Models\Plans\PlanModel;
use App\Models\Plans\ProductModel;
use App\Models\Company\CompanyProfile;
use Carbon\Carbon;
use App\Models\AutoDebitRequest;

class AutoDebitProcessController extends Controller
{
    public function index()
    {
        $agent = session('agent');
        $agentId = $agent->agent_id;
        $existingRequest = AutoDebitRequest::where('agent_id', $agentId)
        ->orderBy('id', 'desc') // Order by ID in descending order
        ->first();
        // dd($existingRequest);
        return view('basic-agent-l.autodebit',compact('existingRequest'));
    }

    public function fetchCustomerData(Request $request)
    {

        // dd($request->all());
        $agent = session('agent');
        $agentId = $agent->agent_id;
        $agents = TeleSalesAgent::where('agent_id',$agentId)->first();
        //  dd($agents);
        $company_id = $agents->company_id;


        $autoDebitRequestData = AutoDebitRequest::where('msisdn', $request->customer_msisdn)
        ->whereDate('created_at', Carbon::today())
        ->first();

        if (!$autoDebitRequestData) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $interested_customer_id = $autoDebitRequestData->interested_customer_id;
        //   dd($interested_customer_id);
        $customer = InterestedCustomer::with(['agent', 'company', 'plan', 'product'])
        ->where('id', $interested_customer_id)
        ->where('deduction_applied', 0)
        ->first();
        //   dd($customer);

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


public function checkConsent(Request $request)
{
    $msisdn = $request->input('msisdn');


    $autoDebitRequestData = AutoDebitRequest::where('msisdn', $msisdn)
    ->whereDate('created_at', Carbon::today())
    ->first();

    if (!$autoDebitRequestData) {
        return response()->json([
            'consistent_provider' => 0,
            'message' => 'No record found'
        ]);
    }

    $interested_customer_id = $autoDebitRequestData->interested_customer_id;
    // Query to check if consistent_provider is 1 for the given MSISDN
    $customer = InterestedCustomer::where('id', $interested_customer_id)
                    ->select('consistent_provider')
                    ->orderBy('id', 'desc') // Order by ID in descending order
                    ->first();

    if ($customer) {
        $message = $customer->consistent_provider == 1
    ? 'Consent for DTMF capture has been successfully recorded.'
    : 'Consent for DTMF capture could not be recorded.';
        return response()->json([
            'consistent_provider' => $customer->consistent_provider,
            'message' => $message
        ]);
    } else {
        return response()->json([
            'consistent_provider' => 0,
            'message' => 'No record found'
        ]);
    }
}


}
