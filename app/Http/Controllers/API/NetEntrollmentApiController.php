<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;
use App\Models\Refund\RefundedCustomer;
use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade;
use Carbon\Carbon;
use App\Models\RecusiveChargingData;
use App\Models\Unsubscription\CustomerUnSubscription;
use Illuminate\Support\Facades\DB;

class NetEntrollmentApiController extends Controller
{
    public function NetEnrollment(Request $request)
    {
        // Check if both startDate and endDate are provided
        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

            // Build the query
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
            ->where('customer_subscriptions.policy_status', '=', '1') // Eager load related models
            ->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);

            // Fetch the data
            $data = $query->get();

            // Prepare the data with headers
            $rows = [];
            foreach ($data as $item) {
                $rows[] = [
                    'Subscription ID' => $item->subscription_id,
                    'Customer MSISDN' => $item->subscriber_msisdn,
                    'Plan Name' => $item->plan_name,
                    'Product Name' => $item->product_name,
                    'Amount' => $item->transaction_amount,
                    'Duration' => $item->product_duration,
                    'Company Name' => $item->company_name,
                    'Agent ID' => $item->sales_agent,
                    'Transaction ID' => $item->cps_transaction_id,
                    'Reference ID' => $item->referenceId,
                    'Next Charging Date' => $item->recursive_charging_date,
                    'Subscription Date' => $item->subscription_time,
                    'Free Look Period' => $item->grace_period_time,
                ];
            }

            $response = [
                'status' => 'Success',
                'message' => 'Your Net Enrollment Get Successfully',
                'NetEnrollment' => $rows,
            ];

            return response()->json($response, 200);
        } else {
            // Return a response indicating that the date range is required
            $response = [
                'status' => 'Error',
                'message' => 'Start date and end date are required to fetch data.',
            ];

            return response()->json($response, 400);
        }
    }




}
