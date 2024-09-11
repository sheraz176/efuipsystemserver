<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Subscription\CustomerSubscription;
use Illuminate\Support\Facades\DB; // Add this line at the beginning of your file
use App\Models\TeleSalesAgent;
use App\Models\AgentCount;
use App\Models\RecusiveChargingData;

class Charts extends Controller
{

    public function getSubscriptionChartData(Request $request)
    {
        $timeRange = $request->input('time_range');

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
        // Fetch data from the database based on monthly active subscriptions
        $data = CustomerSubscription::where('policy_status', 1)
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


    public function getMonthlySubscriptionUnsubscriptionChartData()
    {
        // Fetch data from the database based on monthly subscription and unsubscription counts
        $data = CustomerSubscription::selectRaw('MONTH(subscription_time) as month,
                                        SUM(CASE WHEN policy_status = 1 THEN 1 ELSE 0 END) as subscriptions,
                                        SUM(CASE WHEN policy_status = 0 THEN 1 ELSE 0 END) as unsubscriptions')
            ->groupBy('month')
            ->get();

        // Format the data for the chart
        $labels = [];
        $subscriptionValues = [];
        $unsubscriptionValues = [];

        // Loop through months and set counts
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->where('month', $month)->first();
            $labels[] = Carbon::create()->month($month)->format('F'); // Month name
            $subscriptionValues[] = $monthData ? $monthData->subscriptions : 0;
            $unsubscriptionValues[] = $monthData ? $monthData->unsubscriptions : 0;
        }

        return response()->json(['labels' => $labels, 'subscriptions' => $subscriptionValues, 'unsubscriptions' => $unsubscriptionValues]);
    }

    public function getChartData(Request $request)
{
    $companyId = $request->input('company_id');
    $timePeriod = $request->input('time_period', 'daily');

    $query = CustomerSubscription::where('policy_status', 1);

    if ($companyId) {
        $query->where('company_id', $companyId);
    }

    // Determine the time grouping and data filtering based on the time period
    switch ($timePeriod) {
        case 'daily':
            $query->selectRaw('DATE(created_at) as period, COUNT(*) as count');
            break;
        case 'monthly':
            $query->selectRaw('MONTH(created_at) as period, COUNT(*) as count');
            break;
        case 'last7days':
            $query->where('created_at', '>=', Carbon::now()->subDays(7))
                  ->selectRaw('DATE(created_at) as period, COUNT(*) as count');
            break;
        case 'yearly':
            $query->selectRaw('YEAR(created_at) as period, COUNT(*) as count');
            break;
        case 'hourly':
            $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00") as period, COUNT(*) as count');
            break;
    }

    $data = $query->groupBy('period')
                  ->orderBy('period')
                  ->get();

    $labels = $data->map(function($item) use ($timePeriod) {
        switch ($timePeriod) {
            case 'daily':
            case 'last7days':
                return Carbon::parse($item->period)->format('Y-m-d'); // Format as YYYY-MM-DD
            case 'monthly':
                return Carbon::create()->month($item->period)->format('F'); // Full month name
            case 'yearly':
                return $item->period; // Year
            case 'hourly':
                return Carbon::parse($item->period)->format('Y-m-d H:i'); // Format as YYYY-MM-DD HH:00
        }
    });

    $counts = $data->pluck('count');

    return response()->json([
        'data' => [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Net Enrollment',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]
            ]
        ]
    ]);
}

public function getLineChartData(Request $request)
{
    $companyId = $request->input('company_id');
    $currentTime = now(); // Get current time

    // Query for hourly MSISDN (sales) data
    $query = CustomerSubscription::selectRaw("
        DATE_FORMAT(DATE_ADD(created_at, INTERVAL 1 HOUR), '%Y-%m-%d %H:00:00') as hour, -- Shift to next hour
        COUNT(subscriber_msisdn) as total_msisdn -- Count sales (MSISDN) for each hour
    ")
    ->whereDate('created_at', now()->format('Y-m-d')) // Filter for today's date
    ->where('created_at', '<', $currentTime) // Only include data for completed hours
    ->where('policy_status', 1);

    if ($companyId) {
        $query->where('company_id', $companyId);
    }

    $query->groupBy('hour')
          ->orderBy('hour');

    $results = $query->get();

    // Prepare the data
    $data = [
        'labels' => [],
        'total_msisdn' => [],
        'total_avg' => [], // Total average per hour
        'total_cumulative_msisdn' => [], // Cumulative MSISDN count
        'productivity' => [], // Productivity calculation
        'total_present_agent' => [] // Live agent count per hour
    ];

    $cumulativeMsisdn = 0; // To hold the cumulative MSISDN count

    foreach ($results as $row) {
        // Fetch the latest agent count for the hour
        $agentCount = AgentCount::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') as hour,
            count
        ")
        ->where('company_id', $companyId)
        ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') = ?", [$row->hour])
        ->first();

        // Total present agents for the hour
        $totalPresentAgent = $agentCount ? $agentCount->count : 0;

        // Only proceed if total present agents is greater than 0
        if ($totalPresentAgent > 0) {
            $data['labels'][] = $row->hour;
            $data['total_msisdn'][] = $row->total_msisdn;

            // Calculate cumulative MSISDN count
            $cumulativeMsisdn += $row->total_msisdn;
            $data['total_cumulative_msisdn'][] = $cumulativeMsisdn;

            $data['total_present_agent'][] = $totalPresentAgent;

            // Calculate total average
            $totalAvg = $totalPresentAgent > 0 ? round(($row->total_msisdn / $totalPresentAgent), 2) : 0;
            $data['total_avg'][] = $totalAvg;

            // Calculate productivity (cumulative MSISDN / total present agents)
            $productivity = $totalPresentAgent > 0 ? round(($cumulativeMsisdn / $totalPresentAgent), 2) : 0;
            $data['productivity'][] = $productivity;
        }
    }

    return response()->json($data);
}

public function RecusiveChargingChart(Request $request)
{
    $query = RecusiveChargingData::query();

    // Apply filter for cps_response (Cause)
    if ($request->has('cause') && $request->cause != '') {
        $query->where('cps_response', $request->cause);
    }

    // Initialize an empty array to store chart data
    $chartData = [];

    // Apply time period filter
    if ($request->has('time_period')) {
        $timePeriod = $request->time_period;

        // Handle different time periods
        if ($timePeriod == 'today') {
            $chartData = $query->whereDate('created_at', now()->toDateString())
                               ->selectRaw('COUNT(*) as count, HOUR(created_at) as label')
                               ->groupBy('label')
                               ->get();
        } elseif ($timePeriod == 'monthly') {
            $chartData = $query->selectRaw('COUNT(*) as count, MONTHNAME(created_at) as label')
                               ->groupBy('label')
                               ->get();
        } elseif ($timePeriod == 'last7days') {
            $chartData = $query->whereBetween('created_at', [now()->subDays(7), now()])
                               ->selectRaw('COUNT(*) as count, DAYNAME(created_at) as label')
                               ->groupBy('label')
                               ->get();
        } elseif ($timePeriod == 'yearly') {
            $chartData = $query->selectRaw('COUNT(*) as count, YEAR(created_at) as label')
                               ->groupBy('label')
                               ->get();
        }
    } else {
        // Default to return data for today if no time filter is provided
        $chartData = $query->whereDate('created_at', now()->toDateString())
                           ->selectRaw('COUNT(*) as count, HOUR(created_at) as label')
                           ->groupBy('label')
                           ->get();
    }

    // Return the data as JSON for chart rendering
    return response()->json($chartData);
}






    /////////////////////
    // public function getSubscriptionChartData($timeFrame)
    // {
    //     $data = [];

    //     switch ($timeFrame) {
    //         case 'Today':
    //             $data = $this->getChartDataForToday();
    //             break;
    //         case 'Yesterday':
    //             $data = $this->getChartDataForYesterday();
    //             break;
    //         case 'Last 7 Days':
    //             $data = $this->getChartDataForLast7Days();
    //             break;
    //         case 'Last 30 Days':
    //             $data = $this->getChartDataForLast30Days();
    //             break;
    //         case 'Current Month':
    //             $data = $this->getChartDataForCurrentMonth();
    //             break;
    //         case 'Last Month':
    //             $data = $this->getChartDataForLastMonth();
    //             break;
    //         default:
    //             break;
    //     }

    //     return response()->json($data);
    // }

    // // Function to get chart data for Today
    // private function getChartDataForToday()
    // {
    //     $startDate = Carbon::today();
    //     $endDate = Carbon::tomorrow();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Yesterday
    // private function getChartDataForYesterday()
    // {
    //     $startDate = Carbon::yesterday();
    //     $endDate = Carbon::today();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Last 7 Days
    // private function getChartDataForLast7Days()
    // {
    //     $startDate = Carbon::today()->subDays(6);
    //     $endDate = Carbon::tomorrow();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Last 30 Days
    // private function getChartDataForLast30Days()
    // {
    //     $startDate = Carbon::today()->subDays(29);
    //     $endDate = Carbon::tomorrow();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Current Month
    // private function getChartDataForCurrentMonth()
    // {
    //     $startDate = Carbon::now()->startOfMonth();
    //     $endDate = Carbon::now()->endOfMonth();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to get chart data for Last Month
    // private function getChartDataForLastMonth()
    // {
    //     $startDate = Carbon::now()->subMonth()->startOfMonth();
    //     $endDate = Carbon::now()->subMonth()->endOfMonth();

    //     return $this->fetchChartData($startDate, $endDate);
    // }

    // // Function to fetch chart data based on given date range
    // private function fetchChartData($startDate, $endDate)
    // {
    //     $data = CustomerSubscription::whereBetween('subscription_time', [$startDate, $endDate])
    //         ->selectRaw('MONTH(subscription_time) as month, COUNT(*) as count')
    //         ->groupBy('month')
    //         ->get();

    //     $labels = [];
    //     $values = [];

    //     for ($month = 1; $month <= 12; $month++) {
    //         $monthData = $data->where('month', $month)->first();
    //         $labels[] = Carbon::create()->month($month)->format('F');
    //         $values[] = $monthData ? $monthData->count : 0;
    //     }

    //     return ['labels' => $labels, 'values' => $values];
    // }
}
