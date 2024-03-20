<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;



class CompanyManager extends Model implements Authenticatable
{
    use HasFactory, Notifiable, AuthenticableTrait;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'company_id',
        'phone_number',
        'cnic',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_id');
    }

    public function getAuthIdentifierName()
    {
        return 'username'; // This overrides the default identifier for authentication
    }
}
