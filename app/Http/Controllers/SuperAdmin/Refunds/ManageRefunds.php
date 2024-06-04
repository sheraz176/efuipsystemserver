<?php

namespace App\Http\Controllers\SuperAdmin\Refunds;

use DataTables;
use App\Http\Controllers\Controller;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\Refund\RefundedCustomer;
use App\Models\Company\CompanyProfile;

class ManageRefunds extends Controller
{
    public function index()
    {
        return view('superadmin.refund.refundtable');
    }

    public function getRefundData(Request $request)
    {

        if ($request->ajax()) {
            $todayDate = Carbon::now()->toDateString();
            // Start building the query
            $query = CustomerSubscription::where('grace_period_time', '>=', $todayDate)->where('policy_status', 1);

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






    public function refundReports(Request $request)
    {
        $companies = CompanyProfile::all();
        return view('superadmin.refund.refundreport', compact('companies'));
    }

  public function getRefundedData(Request $request)
{
    if ($request->ajax()) {
        // Start building the query
        $query = RefundedCustomer::select('*');

            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('refunded_customers.refunded_time', '>=', $startDate)
                ->whereDate('refunded_customers.refunded_time', '<=', $endDate);
                // $query->whereBetween('refunded_customers.refunded_time', [$startDate, $endDate]);
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





}
