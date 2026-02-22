<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkManager extends Model
{
    use HasFactory;
    protected $table= 'bulk_manager';
    protected $fillable = [
        'id',
        'msisdn',
        'reason',
        'subsecribe_id',

    ];
}
