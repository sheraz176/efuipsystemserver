<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryNameUpdateLog extends Model
{
    use HasFactory;

            protected $table = 'beneficiary_name_update_logs';

      protected $fillable = [
        'subscriber_msisdn',
        'subscriber_cnic',
        'beneficiary_name',
        'status',
        'api_response'
    ];

}
