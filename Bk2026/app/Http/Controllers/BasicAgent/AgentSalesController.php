<?php

namespace App\Http\Controllers\BasicAgent;

use App\Http\Controllers\Controller;
use App\Models\Plans\PlanModel;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;
use App\Models\Plans\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentSalesController extends Controller
{
    public function sales()
    {
        $agent = session('agent');

        if (!$agent) {
            
            return redirect()->back()->withInput()->withErrors(['login' => 'Session Expired Kindly Re-login']);
        }

        return view('basic-agent.sales', compact('agent'));
    }


    public function transaction()
    {
        $agent = session('agent');
        //$plan_information = PlanModel::all();
        $plan_information = PlanModel::where('status', 1)->get();

        // $plansAndProducts = PlanModel::with('products')->get()->keyBy('plan_id');
        $plansAndProducts = PlanModel::with(['products' => function ($query) {
            $query->select('product_id', 'product_name', 'term_takaful', 'annual_hospital_cash_limit', 'accidental_medicial_reimbursement', 'contribution', 'product_code', 'fee', 'autoRenewal', 'duration', 'status', 'scope_of_cover', 'eligibility', 'other_key_details', 'exclusions', 'plan_id');
        }])
        ->get()
        ->keyBy('plan_id');


        //echo $plansAndProducts;
        

        if (!$agent) {
            
            return redirect()->back()->withInput()->withErrors(['login' => 'Session Expired Kindly Re-login']);
        }
        
        return view('basic-agent.transaction', compact('agent','plan_information', 'plansAndProducts'));
    }


    public function showAgentData()
    {
        $teleSalesAgent = session('agent');

        // Access the agent_id attribute
        $agentId = $teleSalesAgent->agent_id;
        $transactions = CustomerSubscription::where('sales_agent', $agentId)->get();

        return view('basic-agent.SucessSales', compact('transactions'));
    }


    public function FailedAgentReports()
    {
        $teleSalesAgent = session('agent');

        // Access the agent_id attribute
        $agentId = $teleSalesAgent->agent_id;
        $Failedtransactions = FailedSubscription::where('agent_id', $agentId)->get();

        return view('basic-agent.FailedSales', compact('Failedtransactions'));


    }


}
