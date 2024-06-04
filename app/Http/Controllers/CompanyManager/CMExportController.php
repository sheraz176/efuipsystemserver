<?php

namespace App\Http\Controllers\CompanyManager;

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
use Illuminate\Support\Facades\Auth;

class CMExportController extends Controller
{
    public function exportactivesubription(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        //  dd($request->all());
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->where('customer_subscriptions.company_id', '=', $companyId)
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile']); // Eager load related models
         if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
             $dateRange = explode(' to ', $request->input('dateFilter'));
             $startDate = $dateRange[0];
             $endDate = $dateRange[1];
             $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
         }

        $data = $query->get();
        //   dd($data);

      // Define headers
     $headers = ['Subscription ID', 'Customer MSISDN', 'Plan Name', 'Product Name', 'Amount', 'Duration',
     'Company Name', 'Agent Name', 'Transaction ID', 'Reference ID', 'Next Charging Date', 'Subscription Date','Free Look Period']; // Replace with your actual column names
      // Prepare the data with headers
    $rows[] = $headers;
    foreach ($data as $item) {
     $rows[] = [
        $item->subscription_id,
        $item->subscriber_msisdn,
        $item->plan_name,
        $item->product_name,
        $item->transaction_amount,
        $item->product_duration,
        $item->company_name,
        $item->sales_agent,
        $item->cps_transaction_id,
        $item->referenceId,
        $item->recursive_charging_date,
        $item->subscription_time,
        $item->grace_period_time,
    ];
   }

   // Generate XLS file
   $filePath = storage_path('app/TotalSubscriptionReport.xls');
   $file = fopen($filePath, 'w');
   foreach ($rows as $row) {
    fputcsv($file, $row, "\t"); // Tab-delimited for Excel
    }
    fclose($file);

   // Download the file
   return response()->download($filePath)->deleteFileAfterSend(true);


    }

    public function exportcomplatesale(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        // dd($request->all());
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->where('customer_subscriptions.company_id', '=', $companyId)
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile'])
        ->where('customer_subscriptions.policy_status', '=', '1'); // Eager load related models

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
        }
           $data = $query->get();
    //   dd($data);
             // Define headers
     $headers = ['Subscription ID', 'Customer MSISDN', 'Plan Name', 'Product Name', 'Amount', 'Duration',
     'Company Name', 'Agent Name', 'Transaction ID', 'Reference ID', 'Next Charging Date', 'Subscription Date','Free Look Period']; // Replace with your actual column names
      // Prepare the data with headers
    $rows[] = $headers;
    foreach ($data as $item) {
     $rows[] = [
        $item->subscription_id,
        $item->subscriber_msisdn,
        $item->plan_name,
        $item->product_name,
        $item->transaction_amount,
        $item->product_duration,
        $item->company_name,
        $item->sales_agent,
        $item->cps_transaction_id,
        $item->referenceId,
        $item->recursive_charging_date,
        $item->subscription_time,
        $item->grace_period_time,
    ];
   }

   // Generate XLS file
   $filePath = storage_path('app/NetEnrollmentsReport.xls');
   $file = fopen($filePath, 'w');
   foreach ($rows as $row) {
    fputcsv($file, $row, "\t"); // Tab-delimited for Excel
    }
    fclose($file);

   // Download the file
   return response()->download($filePath)->deleteFileAfterSend(true);

    }

    public function exportgetFailedData(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $query = FailedSubscription::select([
            'insufficient_balance_customers.*',
            'plans.plan_name',
            'products.product_name',
            'company_profiles.company_name',
            'tele_sales_agents.username',
            ])
            ->where('insufficient_balance_customers.company_id', '=', $companyId)
            ->join('plans', 'insufficient_balance_customers.planId', '=', 'plans.plan_id')
             ->join('products', 'insufficient_balance_customers.product_id', '=', 'products.product_id')
             ->join('company_profiles', 'insufficient_balance_customers.company_id', '=', 'company_profiles.id')
             ->join('tele_sales_agents', 'insufficient_balance_customers.agent_id', '=', 'tele_sales_agents.agent_id')
             ->with(['plan','product','companyProfile','teleSalesAgent']);
             if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];

                $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
                ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);
            }
        $data = $query->get();

            //  dd($data);
             // Define headers
     $headers = ['Request ID', 'Transaction ID', 'MSISDN', 'Request Time', 'Plan Name', 'Product Name',
     'Amount', 'Refernce ID', 'Result Code', 'Result Summary', 'Company Name', 'Agent Name','Source']; // Replace with your actual column names
      // Prepare the data with headers
    $rows[] = $headers;
    foreach ($data as $item) {
     $rows[] = [
        $item->request_id,
        $item->transactionId,
        $item->accountNumber,
        $item->timeStamp,
        $item->plan_name,
        $item->product_name,
        $item->amount,
        $item->referenceId,
        $item->resultDesc,
        $item->failedReason,
        $item->company_name,
        $item->username,
        $item->source,
    ];
   }

   // Generate XLS file
   $filePath = storage_path('app/UnSuccessFullReport.xls');
   $file = fopen($filePath, 'w');
   foreach ($rows as $row) {
    fputcsv($file, $row, "\t"); // Tab-delimited for Excel
    }
    fclose($file);

   // Download the file
   return response()->download($filePath)->deleteFileAfterSend(true);


    }

    public function companies_cancelled_data_export(Request $request)
  {
    $companyId = Auth::guard('company_manager')->user()->company_id;
    $query = CustomerUnSubscription::select([
        'unsubscriptions.unsubscription_id',
        'customer_subscriptions.subscriber_msisdn',
        'plans.plan_name',
        'products.product_name',
        'customer_subscriptions.transaction_amount',
        'customer_subscriptions.cps_transaction_id',
        'customer_subscriptions.referenceId',
        'customer_subscriptions.subscription_time',
        'unsubscriptions.unsubscription_datetime',
        'unsubscriptions.medium',
        'company_profiles.company_name',
    ])
    ->where('customer_subscriptions.company_id', '=', $companyId)
    ->join('customer_subscriptions', 'customer_subscriptions.subscription_id', '=', 'unsubscriptions.subscription_id')
    ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
    ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
    ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id');

    // Apply filters if provided
    // if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
    //     $query->where('company_profiles.company_id', $request->input('companyFilter'));
    // }

    if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        $query->whereDate('unsubscriptions.unsubscription_datetime', '>=', $startDate)
        ->whereDate('unsubscriptions.unsubscription_datetime', '<=', $endDate);


        $query->addSelect([
            \DB::raw('TIMESTAMPDIFF(SECOND, customer_subscriptions.subscription_time, unsubscriptions.unsubscription_datetime) as subscription_duration')
        ]);
    }

    $data = $query->get();
          //  dd($data);
             // Define headers
             $headers = ['Cacellation ID', 'Customer MSISDN', 'Plan Name', 'Product Name', 'Amount', 'Company Name',
             'Transaction ID', 'Reference ID', 'Subscription Date', 'UnSubscriotion Date']; // Replace with your actual column names
              // Prepare the data with headers
            $rows[] = $headers;
            foreach ($data as $item) {
             $rows[] = [
                $item->unsubscription_id,
                $item->subscriber_msisdn,
                $item->plan_name,
                $item->product_name,
                $item->transaction_amount,
                $item->company_name,
                $item->cps_transaction_id,
                $item->referenceId,
                $item->subscription_time,
                $item->unsubscription_datetime,
            ];
           }

           // Generate XLS file
           $filePath = storage_path('app/CancelledReport.xls');
           $file = fopen($filePath, 'w');
           foreach ($rows as $row) {
            fputcsv($file, $row, "\t"); // Tab-delimited for Excel
            }
            fclose($file);

           // Download the file
           return response()->download($filePath)->deleteFileAfterSend(true);


}

public function RefundedDataExport(Request $request)
{
    $companyId = Auth::guard('company_manager')->user()->company_id;
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
        'customer_subscriptions.subscription_time',
    )
        ->where('customer_subscriptions.company_id', '=', $companyId)
        ->join('customer_subscriptions', 'refunded_customers.subscription_id', '=', 'customer_subscriptions.subscription_id')
        ->join('unsubscriptions', 'customer_subscriptions.subscription_id', '=', 'unsubscriptions.subscription_id')
        ->leftJoin('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->leftJoin('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->leftjoin('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id');// Assuming you pass refunded_id as a parameter

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $refundData->whereDate('unsubscriptions.unsubscription_datetime', '>=', $startDate)
            ->whereDate('unsubscriptions.unsubscription_datetime', '<=', $endDate);

        }

        $data = $refundData->get();
        //   dd($data);
           // Define headers
           $headers = ['Refunded ID', 'Customer MSISDN', 'Plan Name', 'Product Name', 'Amount', 'Company Name',
           'Transaction ID', 'Reference ID', 'Medium','Subscription Date', 'UnSubscriotion Date']; // Replace with your actual column names
            // Prepare the data with headers
          $rows[] = $headers;
          foreach ($data as $item) {
           $rows[] = [
              $item->refund_id,
              $item->subscriber_msisdn,
              $item->plan_name,
              $item->product_name,
              $item->transaction_amount,
              $item->company_name,
              $item->transaction_id,
              $item->reference_id,
              $item->medium,
              $item->subscription_time,
              $item->unsubscription_datetime,

          ];
         }

         // Generate XLS file
         $filePath = storage_path('app/RefundedReport.xls');
         $file = fopen($filePath, 'w');
         foreach ($rows as $row) {
          fputcsv($file, $row, "\t"); // Tab-delimited for Excel
          }
          fclose($file);

         // Download the file
         return response()->download($filePath)->deleteFileAfterSend(true);

}

public function ManageRefundedDataExport(Request $request)
{
    $companyId = Auth::guard('company_manager')->user()->company_id;
    // dd($request->all());
    $todayDate = Carbon::now()->toDateString();
    $query = CustomerSubscription::select([
        'customer_subscriptions.*', // Select all columns from customer_subscriptions table
        'plans.plan_name', // Select the plan_name column from the plans table
        'products.product_name', // Select the product_name column from the products table
        'company_profiles.company_name', // Select the company_name column from the company_profiles table
    ])
         ->where('customer_subscriptions.company_id', '=', $companyId)
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
    }

        $data = $query->get();
        //  dd($data);
           // Define headers
           $headers = ['Subscription ID', 'Customer MSISDN', 'Plan Name', 'Product Name', 'Amount', 'Company Name',
           'Agent Name', 'Next Charging Date', 'Subscription Date', 'Free Look Period']; // Replace with your actual column names
            // Prepare the data with headers
          $rows[] = $headers;
          foreach ($data as $item) {
           $rows[] = [
              $item->subscription_id,
              $item->subscriber_msisdn,
              $item->plan_name,
              $item->product_name,
              $item->transaction_amount,
              $item->company_name,
              $item->sales_agent,
              $item->recursive_charging_date,
              $item->subscription_time,
              $item->grace_period_time,

          ];
         }

         // Generate XLS file
         $filePath = storage_path('app/ManageRefundedReport.xls');
         $file = fopen($filePath, 'w');
         foreach ($rows as $row) {
          fputcsv($file, $row, "\t"); // Tab-delimited for Excel
          }
          fclose($file);

         // Download the file
         return response()->download($filePath)->deleteFileAfterSend(true);

}
public function getDataCompanyExport(Request $request)
{
    $companyId = Auth::guard('company_manager')->user()->company_id;
    // $a=$request->input('dateFilter');
    // dd($a);
    $query = CustomerSubscription::select([
        'customer_subscriptions.*', // Select all columns from customer_subscriptions table
        'plans.plan_name', // Select the plan_name column from the plans table
        'products.product_name', // Select the product_name column from the products table
        'company_profiles.company_name', // Select the company_name column from the company_profiles table
    ])
    ->where('customer_subscriptions.company_id', '=', $companyId)
    ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
    ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
    ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
    ->with(['plan', 'product', 'companyProfile']); // Eager load related models

    if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
        ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
    }

    $data = $query->get();

    //    dd($data);
           // Define headers
           $headers = ['Subscription ID', 'Customer MSISDN', 'Plan Name', 'Product Name', 'Amount','Duration', 'Company Name',
           'Agent Name','Transaction ID', 'Reference ID','Next Charging Date', 'Subscription Date', 'Free Look Period']; // Replace with your actual column names
            // Prepare the data with headers
          $rows[] = $headers;
          foreach ($data as $item) {
           $rows[] = [
              $item->subscription_id,
              $item->subscriber_msisdn,
              $item->plan_name,
              $item->product_name,
              $item->transaction_amount,
              $item->product_duration,
              $item->company_name,
              $item->sales_agent,
              $item->cps_transaction_id,
              $item->referenceId,
              $item->recursive_charging_date,
              $item->subscription_time,
              $item->grace_period_time,

          ];
         }

         // Generate XLS file
         $filePath = storage_path('app/NetEnrollmentReport.xls');
         $file = fopen($filePath, 'w');
         foreach ($rows as $row) {
          fputcsv($file, $row, "\t"); // Tab-delimited for Excel
          }
          fclose($file);

         // Download the file
         return response()->download($filePath)->deleteFileAfterSend(true);


}

public function agents_get_data_export(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $query = CustomerSubscription::select([
            'customer_subscriptions.*', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->where('customer_subscriptions.company_id', '=', $companyId)
        ->join('plans', 'customer_subscriptions.plan_id', '=', 'plans.plan_id')
        ->join('products', 'customer_subscriptions.productId', '=', 'products.product_id')
        ->join('company_profiles', 'customer_subscriptions.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile']) // Eager load related models
        ->where('customer_subscriptions.policy_status', '=', '1'); // Eager load related models

        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
            ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);

        }
        $data = $query->get();

        //    dd($data);
               // Define headers
               $headers = ['Subscription ID', 'Customer MSISDN', 'Plan Name', 'Product Name', 'Amount','Duration', 'Company Name',
               'Agent Name','Transaction ID', 'Reference ID','Next Charging Date', 'Subscription Date', 'Free Look Period']; // Replace with your actual column names
                // Prepare the data with headers
              $rows[] = $headers;
              foreach ($data as $item) {
               $rows[] = [
                  $item->subscription_id,
                  $item->subscriber_msisdn,
                  $item->plan_name,
                  $item->product_name,
                  $item->transaction_amount,
                  $item->product_duration,
                  $item->company_name,
                  $item->sales_agent,
                  $item->cps_transaction_id,
                  $item->referenceId,
                  $item->recursive_charging_date,
                  $item->subscription_time,
                  $item->grace_period_time,

              ];
             }

             // Generate XLS file
             $filePath = storage_path('app/NetEnrollmentReport.xls');
             $file = fopen($filePath, 'w');
             foreach ($rows as $row) {
              fputcsv($file, $row, "\t"); // Tab-delimited for Excel
              }
              fclose($file);

             // Download the file
             return response()->download($filePath)->deleteFileAfterSend(true);


    }

    public function agents_sales_data_export(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $query = FailedSubscription::select([
            'insufficient_balance_customers.request_id', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.transactionId', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.referenceId', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.timeStamp', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.accountNumber', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.resultDesc', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.failedReason', // Select all columns from customer_subscriptions table
            'insufficient_balance_customers.amount', // Select all columns from customer_subscriptions table
            'plans.plan_name', // Select the plan_name column from the plans table
            'products.product_name', // Select the product_name column from the products table
            'company_profiles.company_name', // Select the company_name column from the company_profiles table
        ])
        ->where('insufficient_balance_customers.company_id', '=', $companyId)
        ->join('plans', 'insufficient_balance_customers.planId', '=', 'plans.plan_id')
        ->join('products', 'insufficient_balance_customers.product_id', '=', 'products.product_id')
        ->join('company_profiles', 'insufficient_balance_customers.company_id', '=', 'company_profiles.id')
        ->with(['plan', 'product', 'companyProfile']); // Eager load related models


        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
            ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);

        }

        $data = $query->get();
            //   dd($data);
               // Define headers
               $headers = ['Request ID', 'Transaction ID', 'Refernce ID', 'Sale Request Time', 'Customer Number','Failed Message', 'Failed Information',
               'Amount','Product ID', 'Plan ID','Company']; // Replace with your actual column names
                // Prepare the data with headers
              $rows[] = $headers;
              foreach ($data as $item) {
               $rows[] = [
                  $item->request_id,
                  $item->transactionId,
                  $item->referenceId,
                  $item->timeStamp,
                  $item->accountNumber,
                  $item->resultDesc,
                  $item->failedReason,
                  $item->amount,
                  $item->plan_name,
                  $item->product_name,
                  $item->company_name,

              ];
             }

             // Generate XLS file
             $filePath = storage_path('app/AgentSaleReport.xls');
             $file = fopen($filePath, 'w');
             foreach ($rows as $row) {
              fputcsv($file, $row, "\t"); // Tab-delimited for Excel
              }
              fclose($file);

             // Download the file
             return response()->download($filePath)->deleteFileAfterSend(true);


    }

    public function companies_failed_data_export(Request $request)
{
    $companyId = Auth::guard('company_manager')->user()->company_id;
    $query = FailedSubscription::select([
        'insufficient_balance_customers.request_id', // Select all columns from customer_subscriptions table
        'insufficient_balance_customers.transactionId', // Select all columns from customer_subscriptions table
        'insufficient_balance_customers.referenceId', // Select all columns from customer_subscriptions table
        'insufficient_balance_customers.timeStamp', // Select all columns from customer_subscriptions table
        'insufficient_balance_customers.accountNumber', // Select all columns from customer_subscriptions table
        'insufficient_balance_customers.resultDesc', // Select all columns from customer_subscriptions table
        'insufficient_balance_customers.failedReason', // Select all columns from customer_subscriptions table
        'insufficient_balance_customers.amount', // Select all columns from customer_subscriptions table
        'plans.plan_name', // Select the plan_name column from the plans table
        'products.product_name', // Select the product_name column from the products table
        'company_profiles.company_name', // Select the company_name column from the company_profiles table
    ])
    ->where('insufficient_balance_customers.company_id', '=', $companyId)
    ->join('plans', 'insufficient_balance_customers.planId', '=', 'plans.plan_id')
    ->join('products', 'insufficient_balance_customers.product_id', '=', 'products.product_id')
    ->join('company_profiles', 'insufficient_balance_customers.company_id', '=', 'company_profiles.id')
    ->with(['plan', 'product', 'companyProfile']); // Eager load related models

    if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
        ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);

    }

    $data = $query->get();
        //   dd($data);
               // Define headers
               $headers = ['Request ID', 'Transaction ID', 'Refernce ID', 'Sale Request Time', 'Customer Number','Failed Message', 'Failed Information',
               'Amount','Product ID', 'Plan ID','Company']; // Replace with your actual column names
                // Prepare the data with headers
              $rows[] = $headers;
              foreach ($data as $item) {
               $rows[] = [
                  $item->request_id,
                  $item->transactionId,
                  $item->referenceId,
                  $item->timeStamp,
                  $item->accountNumber,
                  $item->resultDesc,
                  $item->failedReason,
                  $item->amount,
                  $item->plan_name,
                  $item->product_name,
                  $item->company_name,

              ];
             }

             // Generate XLS file
             $filePath = storage_path('app/exported_data.xls');
             $file = fopen($filePath, 'w');
             foreach ($rows as $row) {
              fputcsv($file, $row, "\t"); // Tab-delimited for Excel
              }
              fclose($file);

             // Download the file
             return response()->download($filePath)->deleteFileAfterSend(true);

}


}
