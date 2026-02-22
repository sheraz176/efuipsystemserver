<?php

namespace App\Http\Controllers\SuperAdmin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription\CustomerSubscription;
use App\Models\Subscription\FailedSubscription;
use App\Models\Company\CompanyProfile;
use App\Models\InterestedCustomers\InterestedCustomer;
use App\Models\Unsubscription\CustomerUnSubscription;
use App\Models\RecusiveChargingData;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Models\ConsentData;
use App\Models\RecusiveCounts;
use App\Models\Recusivefailed;
use App\Models\HourlyTransactionSummary;


class SuperAdminReports extends Controller
{



public function hourlySummary(Request $request)
{
    // Handle CSV export
    if ($request->has('export')) {

        $date = $request->dateFilter ?? HourlyTransactionSummary::max('summary_date');

        $data = HourlyTransactionSummary::where('summary_date', $date)
            ->orderBy('hour')
            ->get([
                'hour',
                'call_center_count',
                'call_center_amount',
                'ivr_count',
                'ivr_amount',
                'merchant_count',
                'merchant_amount',
                'app_count',
                'app_amount',
                'recursive_count',
                'recursive_amount',
                'summary_date'
            ]);

        $filename = "hourly_summary_{$date}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $columns = [
            'Hour',
            'Call Center Count',
            'Call Center Amount',
            'IVR Count',
            'IVR Amount',
            'Merchant Count',
            'Merchant Amount',
            'App Count',
            'App Amount',
            'Recursive Count',
            'Recursive Amount',
            'Summary Date'
        ];

        $callback = function() use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->hour,
                    $row->call_center_count,
                    $row->call_center_amount,
                    $row->ivr_count,
                    $row->ivr_amount,
                    $row->merchant_count,
                    $row->merchant_amount,
                    $row->app_count,
                    $row->app_amount,
                    $row->recursive_count,
                    $row->recursive_amount,
                    $row->summary_date->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Otherwise, show view & DataTable
    $latestDate = $request->dateFilter ?? HourlyTransactionSummary::max('summary_date');

    if ($request->ajax()) {
        $query = HourlyTransactionSummary::query();
        if ($request->filled('dateFilter')) {
            $dates = explode(' to ', $request->dateFilter);
            if(count($dates) === 2){
                $query->whereBetween('summary_date', [$dates[0], $dates[1]]);
            } else {
                $query->where('summary_date', $request->dateFilter);
            }
        } else {
            $query->where('summary_date', $latestDate);
        }

        return datatables()->of($query)
            ->editColumn('summary_date', function($row){
                return $row->summary_date->format('Y-m-d');
            })
            ->make(true);
    }

    return view('superadmin.hourlysummary', compact('latestDate'));
}


   public function recusivecountsgetdata(Request $request)
{
    $query = RecusiveCounts::where('status', 1);

    /* ===== DATE FILTER (date column) ===== */
    if ($request->dateFilter) {
        [$from, $to] = explode(' to ', $request->dateFilter);
        $query->whereBetween('date', [$from, $to]);
    }

    $query->orderBy('date', 'desc');

    /* ============== CSV EXPORT ============== */
    if ($request->export === 'csv') {

        $filename = 'recursive_charging_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(function () use ($query) {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Date',
                'Today Recursive',
                'Success Total',
                'Failed Total',
                'Term Life Daily Success',
                'Term Life Monthly Success',
                'Family Health Daily Success',
                'Family Health Monthly Success',
                'Term Life Daily Amount',
                'Term Life Monthly Amount',
                'Family Health Daily Amount',
                'Family Health Monthly Amount',
            ]);

            foreach ($query->get() as $row) {
                fputcsv($file, [
                    $row->date,
                    $row->total_recursive_today,
                    $row->success_total,
                    $row->failed_total,
                    $row->term_life_daily_count,
                    $row->term_life_monthly_count,
                    $row->family_health_daily_count,
                    $row->family_health_monthly_count,
                    $row->term_life_daily_amount,
                    $row->term_life_monthly_amount,
                    $row->family_health_daily_amount,
                    $row->family_health_monthly_amount,
                ]);
            }

            fclose($file);

        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }

    /* ============== DATATABLE RESPONSE ============== */
    return DataTables::of($query)
        ->addIndexColumn()
        ->editColumn('date', fn($row) => $row->date)
        ->make(true);
}

  
 
    public function index()
    {
        return view('superadmin.completesales');
    }

    public function recusivecountsindex()
    {
        return view('superadmin.recusivecountsindex');
    }
    


    public function refundagentindex()
    {
        return view('super_agent_Interested.netenrollment');
    }

    public function getData(Request $request)
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

            ->addColumn('consistent_provider', function($data){
                return $data->consent;
             })

            ->rawColumns(['company_name', 'plan_name', 'product_name','consistent_provider'])
            ->make(true);
    }
}




    public function failed_transactions()
    {
        return view('superadmin.completefailed');
    }

    public function getFailedData(Request $request)
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

            return Datatables::of($query)->addIndexColumn()
                ->addColumn('plan_name', function ($data) {
                    return $data->plan->plan_name;
                })
                ->addColumn('product_name', function ($data) {
                    return $data->product->product_name;
                })
                ->addColumn('company_name', function ($data) {
               return $data->company ? $data->company->company_name : 'No Company';
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


    public function companies_reports()
    {
        $companies = CompanyProfile::all();
        return view('superadmin.company.companywisereports',compact('companies'));
    }

    public function getDataCompany(Request $request)
{

// dd('hi');

    if ($request->ajax()) {
        // Start building the query
        $query = CustomerSubscription::select('*')->where('policy_status' , '1');

        // Apply date filters if provided
              // Apply date filters if provided
        if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
            $dateRange = explode(' to ', $request->input('dateFilter'));
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];
            $query->whereDate('customer_subscriptions.subscription_time', '>=', $startDate)
                  ->whereDate('customer_subscriptions.subscription_time', '<=', $endDate);
        }
        if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
            $query->where('customer_subscriptions.company_id', $request->input('companyFilter'));
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
            ->addColumn('consistent_provider', function($data){
                return $data->consent;
             })

            ->rawColumns(['company_name', 'plan_name', 'product_name','consistent_provider'])
            ->make(true);
    }


}


public function companies_failed_reports()
{
    $companies = CompanyProfile::all();
    return view('superadmin.company.companyfailedreports',compact('companies'));
}

public function companies_failed_data(Request $request)
{

  if ($request->ajax()) {
    // Start building the query
    $query = FailedSubscription::select('*');

  // Apply company filter if provided


    if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
        $dateRange = explode(' to ', $request->input('dateFilter'));
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];
        $query->whereDate('insufficient_balance_customers.sale_request_time', '>=', $startDate)
              ->whereDate('insufficient_balance_customers.sale_request_time', '<=', $endDate);
    }
    if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
        $query->where('insufficient_balance_customers.company_id', $request->input('companyFilter'));
    }

    return Datatables::of($query)->addIndexColumn()
        ->addColumn('plan_name', function ($data) {
            return $data->plan->plan_name;
        })
        ->addColumn('product_name', function ($data) {
            return $data->product->product_name;
        })
       ->addColumn('company_name', function ($data) {
        return $data->company ? $data->company->company_name : 'No Company';
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

public function companies_unsubscribed_reports()
{
    $companies = CompanyProfile::all();
    return view('superadmin.company.companycancelledreports',compact('companies'));
}

public function companies_cancelled_data(Request $request)
{

    if ($request->ajax()) {
        // Start building the query
        $query = CustomerUnSubscription::with(['customer_subscription.company', 'customer_subscription.plan', 'customer_subscription.products'])
            ->select('*')
            ->join('customer_subscriptions', 'unsubscriptions.subscription_id', '=', 'customer_subscriptions.subscription_id');


        // Apply company filter if provided
        if ($request->has('companyFilter') && $request->input('companyFilter') != '') {
            $query->whereHas('customer_subscription.company', function ($q) use ($request) {
                $q->where('id', $request->input('companyFilter'));
            });
        }
          // Custom search functionality for msisdn
          if ($request->has('msisdn') && !empty($request->input('msisdn'))) {
            $msisdn = $request->input('msisdn');
            $query->where('customer_subscriptions.subscriber_msisdn', 'like', '%' . $msisdn . '%');
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
             ->addColumn('consistent_provider', function($data){
                return $data->customer_subscription->consent;
             })
            ->rawColumns(['subscriber_msisdn','cps_transaction_id', 'transaction_amount', 'plan_name', 'product_name', 'company_name', 'subscription_time'])
            ->make(true);
    }



}


public function complete_active_subscription()
{

    return view('superadmin.completeactivecustomers');
}


public function get_active_subscription_data(Request $request)
    {

    if ($request->ajax()) {
          // Start building the query
          $query = CustomerSubscription::select('*');
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
                 ->addColumn('consistent_provider', function($data){
                return $data->consent;
             })

              ->rawColumns(['company_name', 'plan_name', 'product_name','consistent_provider'])
              ->make(true);

      }

    }

    public function recusive_charging_data_index()
    {
        $recusiveChargingDatacount = RecusiveChargingData::whereDate('created_at', Carbon::today())
        ->count();
    return view('superadmin.recusive-charging.index',compact('recusiveChargingDatacount'));
    }

 public function agent_recusive_charging_data_index()
    {
        $recusiveChargingDatacount = RecusiveChargingData::whereDate('created_at', Carbon::today())
        ->count();
      return view('super_agent_Interested.recusivechargingindex',compact('recusiveChargingDatacount'));
    }



    public function get_recusive_charging_data(Request $request)
    {

        // dd('hi');
        // RecusiveChargingData
        if ($request->ajax()) {
            // Start building the query
            $query = RecusiveChargingData::select('*');
            // Apply date filters if provided
            if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('recusive_charging_data.created_at', '>=', $startDate)
                ->whereDate('recusive_charging_data.created_at', '<=', $endDate);
            }
            return Datatables::of($query)->addIndexColumn()
                ->addColumn('plan_name', function($data){
                    return $data->plans->plan_name;
                })
                ->addColumn('product_name', function($data){
                    return $data->product->product_name;
                })
                ->rawColumns(['plan_name','product_name'])
                ->make(true);
        }

    }


    public function ConsentDataIndex()
    {
        $ConsentNumberDatacount = ConsentData::whereDate('created_at', Carbon::today())
        ->count();
        return view('superadmin.consentdata',compact('ConsentNumberDatacount'));
    }

    public function ConsentDataGet(Request $request)
{
    if ($request->ajax()) {
        // Start building the query
        $query = ConsentData::select('*');

              // Apply date filters if provided
              if ($request->has('dateFilter') && $request->input('dateFilter') != '') {
                $dateRange = explode(' to ', $request->input('dateFilter'));
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query->whereDate('consent_data.created_at', '>=', $startDate)
                ->whereDate('consent_data.created_at', '<=', $endDate);
            }
            if ($request->has('causesFilter') && $request->input('causesFilter') != '') {
                $status = $request->input('causesFilter');
                $query->where('consent_data.status', $status);
            }

        return Datatables::of($query)->addIndexColumn()
        ->editColumn('created_at', function ($row) {
            return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
        })
        ->addColumn('plan_name', function($data){
            return $data->plan->plan_name;
        })
        ->addColumn('product_name', function($data){
            return $data->product->product_name;
        })
        ->addColumn('company_name', function($data){
            return $data->company->company_name;
        })
        ->rawColumns(['plan_name','product_name','company_name'])
        ->make(true);

    }
}



}
