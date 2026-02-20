<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HourlyTransactionSummary extends Model
{
    protected $table = 'hourly_transaction_summary';

    protected $fillable = [

        'summary_date',
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
    ];

    protected $casts = [
        'summary_date' => 'date',
        'hour' => 'integer',

        'call_center_count' => 'integer',
        'call_center_amount' => 'decimal:2',

        'ivr_count' => 'integer',
        'ivr_amount' => 'decimal:2',

        'merchant_count' => 'integer',
        'merchant_amount' => 'decimal:2',

        'app_count' => 'integer',
        'app_amount' => 'decimal:2',

        'recursive_count' => 'integer',
        'recursive_amount' => 'decimal:2',
    ];
}
