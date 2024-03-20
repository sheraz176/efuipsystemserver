<?php

namespace App\Models\Unsubscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerUnSubscription extends Model
{
    use HasFactory;

    protected $table = 'unsubscriptions'; // Set the table name if it's different from the model's plural name

    protected $primaryKey = 'unsubscription_id'; // Set the primary key if it's different from 'id'

    protected $fillable = [
        'unsubscription_datetime',
        'medium',
        'subscription_id',
        'refunded_id',
    ];
}
