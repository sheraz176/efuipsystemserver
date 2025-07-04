<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifycode extends Model
{
    use HasFactory;

       protected $primaryKey = 'id';
    protected $table= 'verify_code';
    protected $fillable = ['id',
                            'msisdn',
                            'code',
                            'status',
                        ];
}
