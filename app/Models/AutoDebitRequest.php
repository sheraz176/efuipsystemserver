<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoDebitRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table= 'auto_debit_requests';
    protected $fillable = ['id',
                            'msisdn',
                            'agent_id',

                        ];

}
