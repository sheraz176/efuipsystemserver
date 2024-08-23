<?php

namespace App\Http\Controllers\customerInformation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Company\CompanyProfile;
use App\Models\Plans\ProductModel;
use App\Models\Plans\PlanModel;
use App\Models\TeleSalesAgent;

class CustomerInformationController extends Controller
{
    public function index()
    {

        return view('superadmin.customerInformation.index');
    }

    public function search(Request $request)
    {
        $msisdn = $request->input('msisdn');

        $customers = CustomerSubscription::with(['companyProfiles', 'products', 'plan', 'teleSalesAgent'])
                                         ->where('subscriber_msisdn', $msisdn)
                                         ->get();

        if ($customers->isEmpty()) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return view('superadmin.customerInformation.partials.customer_info', compact('customers'));
    }

    public function CompanyMangerindex()
    {

        return view('company_manager.customerInformation.index');
    }

    public function CompanyMangersearch(Request $request)
    {
        // dd($request->all());
        $msisdn = $request->input('msisdn');
        $company_id = $request->input('company_id');

        $customers = CustomerSubscription::with(['companyProfiles', 'products', 'plan', 'teleSalesAgent'])
                                         ->where('subscriber_msisdn', $msisdn)->where('company_id',$company_id)
                                         ->get();

        if ($customers->isEmpty()) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return view('company_manager.customerInformation.partials.customer_info', compact('customers'));
    }

    public function BasicAgentindex()
    {

        return view('basic-agent.customerInformation.index');
    }

    public function BasicAgentsearch(Request $request)
    {
        //  dd($request->all());
        $msisdn = $request->input('msisdn');
        $agent_id = $request->input('agent_id');

        $customers = CustomerSubscription::with(['companyProfiles', 'products', 'plan', 'teleSalesAgent'])
                                         ->where('subscriber_msisdn', $msisdn)->where('sales_agent',$agent_id)
                                         ->get();

        if ($customers->isEmpty()) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return view('basic-agent.customerInformation.partials.customer_info', compact('customers'));
    }

    public function BasicAgentLindex()
    {

        return view('basic-agent-l.customerInformation.index');
    }

    public function BasicAgentLsearch(Request $request)
    {
        //  dd($request->all());
        $msisdn = $request->input('msisdn');
        $agent_id = $request->input('agent_id');

        $customers = CustomerSubscription::with(['companyProfiles', 'products', 'plan', 'teleSalesAgent'])
                                         ->where('subscriber_msisdn', $msisdn)->where('sales_agent',$agent_id)
                                         ->get();

        if ($customers->isEmpty()) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return view('basic-agent-l.customerInformation.partials.customer_info', compact('customers'));
    }

}
