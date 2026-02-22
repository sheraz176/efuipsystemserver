<?php

namespace App\Models\SuperAgent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class SuperAgentModel extends Model implements Authenticatable
{
    use HasFactory, Notifiable, AuthenticableTrait;

    protected $table = 'super_agents';
    protected $primaryKey = 'super_agent_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'username', 'status', 'islogin', 'call_status',
        'today_login_time', 'today_logout_time', 'email', 'company_id', 'password', 'session_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
