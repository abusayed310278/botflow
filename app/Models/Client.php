<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'client_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // your table has no created_at/updated_at

    protected $fillable = [
        'name','email','username','admin_type','password','telephone',
        'balance','balance_type','debit_limit','spent',
        'register_date','login_date','login_ip','apikey',
        'tel_type','email_type','client_type',
        'access','lang','timezone','currency_type','ref_code','ref_by',
        'change_email','resend_max','currency','passwordreset_token',
        'coustm_rate','verified',
    ];

    // keep secrets out of API responses
    protected $hidden = [
        'password','apikey','passwordreset_token',
    ];

    protected $casts = [
        'balance'       => 'decimal:4',
        'spent'         => 'decimal:4',
        'debit_limit'   => 'float',
        'register_date' => 'datetime',
        'login_date'    => 'datetime',
        'timezone'      => 'float',
        'resend_max'    => 'integer',
        'coustm_rate'   => 'integer',
    ];
}
