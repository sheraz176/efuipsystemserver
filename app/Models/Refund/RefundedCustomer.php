<?php

namespace App\Models\Refund;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundedCustomer extends Model
{
    use HasFactory;
    protected $table = 'refunded_customers'; // Set the table name if it's different from the model's plural name

    protected $primaryKey = 'refund_id'; // Set the primary key if it's different from 'id'

    protected $fillable = [
        'subscription_id',
        'unsubscription_id',
        'transaction_id',
        'reference_id',
        'cps_response',
        'result_description',
        'result_code',
        'refunded_by',
        'medium',
    ];
}
