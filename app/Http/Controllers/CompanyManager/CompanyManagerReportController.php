<?php

namespace App\Http\Controllers\CompanyManager;
use DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;
use App\Models\Company\CompanyProfile;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\RecusiveChargingData;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\TeleSalesAgent;
use App\Models\Refund\RefundedCustomer;


class CompanyManagerReportController extends Controller
{
    public function complete_sales_index()
    {
        return view('company_manager.reports.completesales');
    }
    public function getData(Request $request)
    {
        // dd('hi');
        $companyId = Auth::guard('company_manager')->user()->company_id;
        if ($request->ajax()) {
            // Start building the query
            $query = CustomerSubscription::select('*')->where('policy_status' , '1')->where('company_id' , $companyId);

                  // Apply date filters if provided
            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
                      ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
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

    public function failed_transactions()
    {
        return view('company_manager.reports.completefailed');
    }

    public function getFailedData(Request $request)
    {
        // dd('hi');
        $companyId = Auth::guard('company_manager')->user()->company_id;
        if ($request->ajax()) {
            // Start building the query
            $query = FailedSubscription::select('*')->where('company_id' , $companyId);

            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
                      ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);
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

    public function complete_active_subscription()
{

    return view('company_manager.reports.completeactivecustomers');
}


public function activecustomerdataget(Request $request)
    {

        // dd('hi');
        $companyId = Auth::guard('company_manager')->user()->company_id;
        if ($request->ajax()) {
            // Start building the query
            $query = CustomerSubscription::select('*')->where('company_id' , $companyId);

                  // Apply date filters if provided
            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
                      ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
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

    public function companies_unsubscribed_reports()
   {
    $companyId = Auth::guard('company_manager')->user()->company_id;
    $companies = CompanyProfile::where('company_id',$companyId)->get();
    return view('company_manager.reports.companycancelledreports',compact('companies'));
   }

   public function companies_cancelled_data(Request $request)
   {
    // dd('hi');
    $companyId = Auth::guard('company_manager')->user()->company_id;
    if ($request->ajax()) {
        // Start building the query

            $query = CustomerUnSubscription::whereHas('customer_subscription', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            });
        // Apply company filter if provided
        if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
            $query->whereHas('customer_subscription.company', function ($q) use ($request) {
                $q->where('id', $request->input('companyFilter'));
            });
        }

    if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];
        $query->whereDate('unsubscriptions.unsubscription_datetime', '>=', $startDate)
        ->whereDate('unsubscriptions.unsubscription_datetime', '<=', $endDate);

    }

        return Datatables::of($query)->addIndexColumn()
            ->addColumn('subscriber_msisdn', function ($data) {
                return $data->customer_subscription->subscriber_msisdn;
            })
            ->addColumn('transaction_amount', function ($data) {
                return $data->customer_subscription->transaction_amount;
            })
            ->addColumn('plan_name', function ($data) {
                return $data->customer_subscription->plan->plan_name;
            })
            ->addColumn('product_name', function ($data) {
                return $data->customer_subscription->products->product_name;
            })
            ->addColumn('company_name', function ($data) {
                return $data->customer_subscription->company->company_name;
            })
            ->addColumn('cps_transaction_id', function ($data) {
                return $data->customer_subscription->cps_transaction_id;
            })
            ->addColumn('referenceId', function ($data) {
                return $data->customer_subscription->referenceId;
            })
            ->addColumn('subscription_time', function ($data) {
                return $data->customer_subscription->subscription_time;
            })
            ->rawColumns(['subscriber_msisdn','cps_transaction_id', 'transaction_amount', 'plan_name', 'product_name', 'company_name', 'subscription_time'])
            ->make(true);
    }


    }

    public function refundReports(Request $request)
    {
        $companies = CompanyProfile::all();
        return view('company_manager.reports.refundreport', compact('companies'));
    }

    public function getRefundedData(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;

        if ($request->ajax()) {
            // Start building the query
            $query = RefundedCustomer::whereHas('customer_subscription', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            });

                if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                    $dateRange = explode(' to ', $request->input('dateFilter'));
                    $startDate = $dateRange[0];
                    $endDate = $dateRange[1];
                    $query->whereDate('refunded_customers.refunded_time', '>=', $startDate)
                    ->whereDate('refunded_customers.refunded_time', '<=', $endDate);
                    // $query->whereBetween('refunded_customers.refunded_time', [$startDate, $endDate]);
                }

                     // Add custom search functionality for numeric columns
                     if ($request->has('msisdn') && !empty($request->input('msisdn'))) {
                        $msisdn = $request->input('msisdn');
                        $query->whereHas('customer_subscription', function ($query) use ($msisdn) {
                            $query->where('subscriber_msisdn', 'like', '%' . $msisdn . '%');
                        });
                    }

            return Datatables::of($query)->addIndexColumn()
                ->addColumn('subscriber_msisdn', function ($data) {
                    return $data->customer_subscription->subscriber_msisdn;
                })
                ->addColumn('transaction_amount', function ($data) {
                    return $data->customer_subscription->transaction_amount;
                })
                ->addColumn('plan_name', function ($data) {
                    return $data->customer_subscription->plan->plan_name;
                })
                ->addColumn('product_name', function ($data) {
                    return $data->customer_subscription->products->product_name;
                })
                ->addColumn('company_name', function ($data) {
                    return $data->customer_subscription->company->company_name;
                })
                ->addColumn('subscription_time', function ($data) {
                    return $data->customer_subscription->subscription_time;
                })
                ->addColumn('unsubscription_datetime', function ($data) {
                    $data_count = count($data->customer_unsubscription);
                    if ($data_count > 0) {
                        return $data->customer_unsubscription[$data_count - 1]->unsubscription_datetime;
                    } else {
                        return "";
                    }
                })
                ->rawColumns(['subscriber_msisdn', 'transaction_amount', 'plan_name', 'product_name', 'company_name', 'subscription_time', 'unsubscription_datetime'])
                ->make(true);
        }


    }

    public function manage_refund_index()
    {
        return view('company_manager.reports.refundtable');
    }

    public function getRefundData(Request $request)
    {
        // dd($request->all);
        $todayDate = Carbon::now()->toDateString();
        $companyId = Auth::guard('company_manager')->user()->company_id;

         if ($request->ajax()) {
            $todayDate = Carbon::now()->toDateString();
            // Start building the query
            $query = CustomerSubscription::where('grace_period_time', '>=', $todayDate)->where('policy_status', 1)->where('company_id' , $companyId);

            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
                ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
                // $query->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
            }

            // Add custom search functionality for numeric columns
            if ($request->has('msisdn') && !empty($request->input('msisdn'))) {
                $msisdn = $request->input('msisdn');
                $query->where('customer_subscriptions.subscriber_msisdn', 'like', '%' . $msisdn . '%');
            }


            return Datatables::of($query)->addIndexColumn()

                ->addColumn('plan_name', function ($data) {
                    return $data->plan->plan_name;
                })
                ->addColumn('product_name', function ($data) {
                    return $data->products->product_name;
                })
                ->addColumn('company_name', function ($data) {
                    return $data->company->company_name;
                })

                ->rawColumns(['plan_name', 'product_name', 'company_name'])
                ->make(true);
        }


    }

    public function agents_Subscriptions()
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $agents = TelesalesAgent::where('company_id', $companyId)->get();
        return view('company_manager.reports.agentwisereports',compact('agents'));
    }


    public function agents_get_data(Request $request)
    {
        // dd('hi');
        $companyId = Auth::guard('company_manager')->user()->company_id;

        if ($request->ajax()) {
            // Start building the query
            $query = CustomerSubscription::select('*')->where('policy_status' , '1')->where('company_id' , $companyId);

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
        $agents = TeleSalesAgent::all();
        return view('company_manager.reports.agentsalerequest',compact('agents'));
    }

    public function agents_sales_data(Request $request)
    {
        // dd('hi');
        $companyId = Auth::guard('company_manager')->user()->company_id;
        if ($request->ajax()) {
            // Start building the query
            $query = FailedSubscription::select('*')->where('company_id' , $companyId);

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

    public function check_agent_status()
    {
        // dd('hi');
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $telesalesAgents = TelesalesAgent::where('company_id', $companyId)->get();
        return view('company_manager.agent-status', compact('telesalesAgents'));
    }

}
