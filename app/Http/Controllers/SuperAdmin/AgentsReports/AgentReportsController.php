<?php

namespace App\Http\Controllers\SuperAdmin\AgentsReports;

use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeleSalesAgent;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;

class AgentReportsController extends Controller
{
    public function agents_Subscriptions()
    {
        $agents = TeleSalesAgent::all();
        return view('superadmin.agent.agentwisereports',compact('agents'));
    }


    public function agents_get_data(Request $request)
    {

      if ($request->ajax()) {
        // Start building the query
        $query = CustomerSubscription::select('*')->where('policy_status' , '1');

              // Apply date filters if provided
        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
            $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
                  ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
        }

        if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
            $query->where('customer_subscriptions.sales_agent', $request->input('companyFilter'));
        }

        return Datatables::of($query)->addIndexColumn()

            ->addColumn('company_name', function($data){
                return $data->company->company_name;
            })
            ->addColumn('plan_name', function($data){
                return $data->plan->plan_name;
            })
            ->addColumn('product_name', function($data){
                return $data->products->product_name;
            })

            ->rawColumns(['company_name', 'plan_name', 'product_name'])
            ->make(true);
      }

    }


    public function agents_sales_request()
    {
        $agents = TeleSalesAgent::get();
        return view('superadmin.agent.agentsalerequest',compact('agents'));
    }

    public function agents_sales_data(Request $request)
    {

        if ($request->ajax()) {
            // Start building the query
            $query = FailedSubscription::select('*');

            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
                      ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);
            }
            if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
                $query->where('insufficient_balance_customers.agent_id', $request->input('companyFilter'));
            }

            return Datatables::of($query)->addIndexColumn()
                ->addColumn('plan_name', function ($data) {
                    return $data->plan->plan_name;
                })
                ->addColumn('product_name', function ($data) {
                    return $data->product->product_name;
                })
                ->addColumn('company_name', function ($data) {
                    return $data->company->company_name;
                })
                ->addColumn('username', function ($data) {
                    if ($data->teleSalesAgent) {
                        return $data->teleSalesAgent->username;
                    }
                    return 'N/A'; // Or any other default value you prefer
                })
                ->rawColumns(['plan_name', 'product_name', 'company_name', 'username'])
                ->make(true);
        }

    }


}
