<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSMsisdn extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table= 's_m_s_msisdns';
    protected $fillable = ['id',
                            'msisdn',
                            'plan_id',
                            'product_id',
                            'response',
                            'status',
                        ];

}
