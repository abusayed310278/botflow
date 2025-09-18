<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
        protected $fillable = [
        'service',
        'name',
        'type',
        'rate',
        'custom_rate',
        'min',
        'max',
        'dripfeed',
        'refill',
        'cancel',
        'category',
        'update_price',
    ];

    public function updates()
    {
        return $this->hasMany(Update::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function additionalsUpdates()
    {
        return $this->hasMany(AdditionalsUpdate::class, 'service_id', 'service');
    }
}