<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Table & PK match your migration (id auto-increment)
    protected $table = 'orders';

    // No default timestamps in your migration; add $timestamps = true if you later add created_at/updated_at.
    public $timestamps = false;

    protected $fillable = [
        'service_id',
        'order_url',

        'order_api',
        'order_status',
        'order_error',
        'order_detail',

        'order_quantity',
        'order_charge',
        'order_extra',

        'order_start',
        'order_remains',
        'last_check',

        'api_charge',
        'api_currencycharge',
        'api_orderid',
        'api_serviceid',

        'order_profit',

        // Dripfeed / subscriptions
        'dripfeed',
        'dripfeed_id',
        'dripfeed_status',
        'dripfeed_totalcharges',
        'dripfeed_runs',
        'dripfeed_delivery',
        'dripfeed_interval',
        'dripfeed_totalquantity',

        'subscriptions_type',
        'new_subscriptions_id',
        'subscriptions_status',
        'subscriptions_username',
        'subscriptions_post',
        'subscriptions_delivery',
        'subscriptions_delay',
        'subscriptions_min',
        'subscriptions_max',
        'subscriptions_expiry',

        'country_id',

        'refill',
    ];

    protected $casts = [
        'client_id'            => 'integer',
        'service_id'           => 'integer',
        'order_api'            => 'integer',

        'order_quantity'       => 'integer',
        'order_charge'         => 'decimal:4',
        'order_extra'          => 'decimal:4',

        'order_start'          => 'integer',
        'order_remains'        => 'integer',
        'last_check'           => 'datetime',

        'api_charge'           => 'decimal:4',
        'api_currencycharge'   => 'decimal:4',
        'api_serviceid'        => 'integer',

        'order_profit'         => 'decimal:4',

        'dripfeed'             => 'integer',
        'dripfeed_id'          => 'integer',

        'subscriptions_type'   => 'integer',
        'subscriptions_id'     => 'integer',

        'country_id'           => 'integer',

        'refill'               => 'integer',
    ];

    /* ========= Relationships ========= */

    // FK points to clients.client_id (not id)
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    // FK points to services.service_id (not id)
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function dripfeed()
    {
        return $this->belongsTo(Dripfeed::class, 'dripfeed_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id'); 
        // change 'id' if countries PK is country_id
    }

    public function newSubscription()
    {
        return $this->belongsTo(NewSubscription::class, 'new_subscriptions_id', 'id');
    }

}
