<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class interestedCustomerData extends Model
{
    use HasFactory;
    protected $table = 'interesed_customer_data';
    protected $fillable = [
        'subscriber_msisdn',
        'customer_cnic',
        'plan_id',
        'product_id',
        'beneficiary_msisdn',
        'beneficiary_cnic',
        'beneficiary_name',
        'agent_id',
        'company_id',
        'amount'
    ];
}
