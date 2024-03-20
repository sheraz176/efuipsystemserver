<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;


    protected $fillable = [
        'company_id',
        'company_name',
        'company_code',
        'company_address',
        'company_poc_name',
        'company_poc_number',
        'company_email',
        'company_phone_number',
        'company_status',
    ];
}
