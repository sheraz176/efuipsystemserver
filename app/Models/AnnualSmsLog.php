<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualSmsLog extends Model
{
    use HasFactory;
        protected $table = 'annual_sms_log';

    protected $fillable = [
        'subscriber_msisdn',
        'message',
        'api_response',
        'sent_date'
    ];

}
