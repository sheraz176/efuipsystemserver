<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logs extends Model
{
    use HasFactory;
    protected $table= 'logs';
    protected $fillable = [
        'id',
        'msisdn',
        'resultCode',
        'resultDesc',
        'transaction_id',
        'reference_id',
        'cps_response',
        'api_url',
        'response_encrypted_data',
        'response_decrypted_data',
        'source',
        'super_agent_name',
        'agent_id'

    ];
}
