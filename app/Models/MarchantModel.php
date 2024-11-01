<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarchantModel extends Model
{
    use HasFactory;
    protected $table = 'marchant_data';
    protected $fillable = [
        'id',
        'marchant_msisdn',
        'amount',
        'customer_msisdn',
        'reason',
        'status',
    ];

}
