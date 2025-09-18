<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dripfeed extends Model
{
    protected $table = 'dripfeeds';

    protected $fillable = [
        'dripfeed_status',
        'subscriptions_type',
        'dripfeed_totalcharges',
        'dripfeed_runs',
        'dripfeed_delivery',
        'dripfeed_totalquantity',
    ];

    protected $casts = [
        'subscriptions_type'      => 'integer',
        'dripfeed_totalcharges'   => 'decimal:2',
        'dripfeed_runs'           => 'integer',
        'dripfeed_delivery'       => 'integer',
        'dripfeed_totalquantity'  => 'integer',
    ];

     public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
