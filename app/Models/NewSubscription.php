<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Provider;

class NewSubscription extends Model
{
    use HasFactory;

    protected $table = 'new_subscriptions';

    protected $fillable = [
        'service_name','category_id','servicetype','service_package',
        'service_api','service_price','service_min','service_max',
        'service_speed','price_type','api_alert','status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'service_api');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
