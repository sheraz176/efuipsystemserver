<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $table = 'claims';
    use HasFactory;

    protected $fillable = [
        'msisdn',
        'plan_id',
        'product_id',
        'status',
        'date',
        'amount',
        'type',
        'history_name',
        'doctor_prescription',
        'medical_bill',
        'lab_bill',
        'other',
        'claim_amount',
    ];

}
