<?php

namespace App\Http\Controllers\CompanyManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription\CustomerSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionChartController extends Controller
{

    public function getSubscriptionChartData(Request $request)
    {
        $timeRange = $request->input('time_range');
        $companyId = Auth::guard('company_manager')->user()->company_id;
        // Define the time range intervals based on the selected time range
        switch ($timeRange) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::tomorrow();
                $interval = 'hour';
                break;
            case 'yesterday':
                $start = Carbon::yesterday();
                $end = Carbon::today();
                $interval = 'hour';
                break;
            case 'this_year':
                // Fetch data for this year and group into months
                $data = CustomerSubscription::whereYear('subscription_time', Carbon::now()->year)
                    ->where('company_id', $companyId)
                    ->selectRaw("DATE_FORMAT(subscription_time, '%m-%Y') as label, COUNT(*) as count")
                    ->groupBy('label')
                    ->get();

                // Initialize labels and values
                $labels = [];
                $values = [];

                // Generate labels for each month of the year
                for ($month = 1; $month <= 12; $month++) {
                    $labels[] = str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . Carbon::now()->year;
                    $values[str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . Carbon::now()->year] = 0; // Initialize count to 0
                }

                // Fill in counts from fetched data
                foreach ($data as $subscription) {
                    $values[$subscription->label] = $subscription->count;
                }

                // Prepare labels and values for response
                $formattedLabels = array_keys($values);
                $formattedValues = array_values($values);

                return response()->json(['labels' => $formattedLabels, 'values' => $formattedValues]);

                break;
            case 'last_7_days':
                $start = Carbon::now()->subDays(7);
                $end = Carbon::now()->addDay();
                $interval = 'day';
                break;
            case 'last_30_days':
                $start = Carbon::now()->subDays(30);
                $end = Carbon::now()->addDay();
                $interval = 'day';
                break;
            case 'current_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth()->addDay();
                $interval = 'day';
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth()->addDay();
                $interval = 'day';
                break;
            default:
                // Handle invalid time range
                return response()->json(['error' => 'Invalid time range']);
        }

        // Fetch data based on the selected time range
        $data = CustomerSubscription::whereBetween('subscription_time', [$start, $end])
           ->where('company_id', $companyId)
            ->selectRaw("DATE_FORMAT(subscription_time, '%Y-%m-%d" . ($interval === 'hour' ? ' %H:00:00' : '') . "') as label, COUNT(*) as count")
            ->groupBy('label')
            ->get();

        // Generate labels with the complete range of time periods
        $labels = [];
        $values = [];
        $current = clone $start;
        while ($current < $end) {
            $formattedLabel = $current->format('Y-m-d' . ($interval === 'hour' ? ' H:00:00' : ''));
            $labels[] = $formattedLabel;
            $values[$formattedLabel] = 0; // Initialize count to 0
            $current->add($interval === 'hour' ? '1 hour' : '1 ' . $interval);
        }

        // Fill in counts from fetched data
        foreach ($data as $subscription) {
            $values[$subscription->label] = $subscription->count;
        }

        // Prepare labels and values for response
        $formattedLabels = array_keys($values);
        $formattedValues = array_values($values);

        return response()->json(['labels' => $formattedLabels, 'values' => $formattedValues]);
    }

    public function getMonthlyActiveSubscriptionChartData()
    {
        $companyId = Auth::guard('company_manager')->user()->company_id;
        // Fetch data from the database based on monthly active subscriptions
        $data = CustomerSubscription::where('policy_status', 1)
            ->where('company_id', $companyId)
            ->selectRaw('MONTH(subscription_time) as month, COUNT(*) as count')
            ->groupBy('month')
            ->get();

        // Format the data for the chart
        $labels = [];
        $values = [];

        // Loop through months and set counts
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->where('month', $month)->first();
            $labels[] = Carbon::create()->month($month)->format('F'); // Month name
            $values[] = $monthData ? $monthData->count : 0;
        }

        return response()->json(['labels' => $labels, 'values' => $values]);
    }




}
