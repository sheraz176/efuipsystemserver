<?php

namespace App\Models\Super;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;


class SuperAdmin extends Model implements Authenticatable
{
    use HasFactory, Notifiable, AuthenticableTrait;

    protected $table = 'super_admins';
    protected $primaryKey = 'id';

    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'permissions', 'status', 'session_token', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getAuthIdentifierName()
    {
        return 'username'; // Specify the identifier field for authentication
    }
}
