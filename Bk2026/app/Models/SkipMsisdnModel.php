<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkipMsisdnModel extends Model
{

 protected $table= 'skip_msisdns';

    use HasFactory;
      protected $fillable = [
        'id',
        'lastcharging',
        'subscription_id',
        'msisdn',
        'reason',
    ];

}
