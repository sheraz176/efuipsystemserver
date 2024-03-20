<?php

namespace App\Http\Controllers\CompanyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerSubscription;
use Carbon\Carbon;


class SubscriptionChart extends Controller
{
    public function fetchSubscriptionData(Request $request)
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        $range = $request->input('range');

        switch ($range) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(6);
                $endDate = Carbon::now();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(29);
                $endDate = Carbon::now();
                break;
            case 'current_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            default:
                $startDate = null;
                $endDate = null;
                break;
        }

        $subscriptions = CustomerSubscription::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Prepare data for the chart
        $labels = [];
        $data = [];

        // You can customize the data structure based on your chart library requirements
        foreach ($subscriptions as $subscription) {
            // Assuming you have some logic to calculate counts or sums based on your requirements
            // Here, I'm just pushing some sample data
            $labels[] = $subscription->created_at->format('Y-m-d'); // Assuming date format
            $data[] = 1; // Increment count or sum accordingly
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
