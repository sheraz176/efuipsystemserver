<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentNumber extends Model
{
    use HasFactory;


    protected $table = 'consent_number';

    protected $fillable = [
        'id',
        'msisdn',
        'resultCode',
        'response',
        'consent',
        'count',
        'status',
        'customer_cnic',
        'beneficinary_name',
        'beneficiary_msisdn',
        'agent_id',
        'company_id',
        'planId',
        'productId',
        'amount',

    ];




}
