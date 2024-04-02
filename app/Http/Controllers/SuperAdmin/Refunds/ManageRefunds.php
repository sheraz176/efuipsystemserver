<?php

namespace App\Http\Controllers\SuperAdmin\Refunds;

use DataTables;
use App\Http\Controllers\Controller;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
        $todayDate = Carbon::now()->toDateString();
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
            ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
            ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
            ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
            ->with(['plan', 'product', 'companyProfile'])
            ->where('grace_period_time', '>=', $todayDate) // Eager load related models
            ->where('policy_status', '=', 1);

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

        // Use DataTables for pagination and server-side processing
        return DataTables::eloquent($query)->toJson();
    }






    public function refundReports(Request $request)
    {
        $companies = CompanyProfile::all();
        return view('superadmin.refund.refundreport', compact('companies'));
    }

    public function getRefundedData(Request $request)
    {
        $refundData = RefundedCustomer::select(
            'refunded_customers.refund_id as refund_id',
            'customer_subscriptions.subscriber_msisdn',
            'customer_subscriptions.transaction_amount',
            'unsubscriptions.unsubscription_datetime',
            'refunded_customers.transaction_id',
            'refunded_customers.reference_id',
            'refunded_customers.refunded_by',
            'plans.plan_name',
            'products.product_name',
            'company_profiles.company_name',
            'refunded_customers.medium',
            'customer_subscriptions.subscription_time'
        )
            ->join('customer_subscriptions', 'refunded_customers.subscription_id', '=', 'customer_subscriptions.subscription_id')
            ->join('unsubscriptions', 'customer_subscriptions.subscription_id', '=', 'unsubscriptions.subscription_id')
            ->leftJoin('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
            ->leftJoin('products', 'customer_subscriptions.productId', '=', 'products.product_id')
            ->leftjoin('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id');// Assuming you pass refunded_id as a parameter

            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];

                // $refundData->whereBetween('customer_subscriptions.subscription_time', [$startDate, $endDate]);
                $refundData->whereDate('unsubscriptions.unsubscription_datetime', '>=', $startDate)
                ->whereDate('unsubscriptions.unsubscription_datetime', '<=', $endDate);
            }
               // Add custom search functionality for numeric columns
           if ($request->has('msisdn') && !empty($request->input('msisdn'))) {
            $msisdn = $request->input('msisdn');
            $refundData->where('customer_subscriptions.subscriber_msisdn', 'like', '%' . $msisdn . '%');
            }


            return DataTables::eloquent($refundData)->toJson();
    }
}
