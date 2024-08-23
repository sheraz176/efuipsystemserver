<?php

namespace App\Http\Controllers\BasicAgentL;

use App\Http\Controllers\Controller;
use App\Models\Plans\PlanModel;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;
use App\Models\Plans\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class AgentSalesBasicAgentLController extends Controller
{
    public function sales()
    {
        $agent = session('agent');

        if (!$agent) {

            return redirect()->back()->withInput()->withErrors(['login' => 'Session Expired Kindly Re-login']);
        }

        return view('basic-agent-l.sales', compact('agent'));
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

        return view('basic-agent-l.transaction', compact('agent','plan_information', 'plansAndProducts'));
    }


    public function showAgentData(Request $request)
    {
        $teleSalesAgent = session('agent');

        // Access the agent_id attribute
        $agentId = $teleSalesAgent->agent_id;

        if ($request->ajax()) {
            // Start building the query
            $query = CustomerSubscription::select('*')->where('sales_agent', $agentId)->get();


            return Datatables::of($query)->addIndexColumn()


                ->addColumn('plan_name', function($data){
                    return $data->plan->plan_name;
                })
                ->addColumn('product_name', function($data){
                    return $data->products->product_name;
                })
                ->addColumn('policy_status', function($data) {
                    if ($data->policy_status == 1) {
                        return '<span style="color: green;">Active</span>';
                    } else {
                        return '<span style="color: red;">In Active</span>';
                    }
                })


                ->rawColumns(['plan_name', 'product_name','policy_status'])
                ->make(true);
        }


        return view('basic-agent-l.SucessSales');
    }


    public function FailedAgentReports(Request $request)
    {
        $teleSalesAgent = session('agent');

        // Access the agent_id attribute
        $agentId = $teleSalesAgent->agent_id;


        if ($request->ajax()) {
            // Start building the query
            $query = FailedSubscription::select('*')->where('agent_id', $agentId)->get();


            return Datatables::of($query)->addIndexColumn()
                ->make(true);
        }


        return view('basic-agent-l.FailedSales');


    }


}
