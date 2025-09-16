<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalsUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'status',
        'description',
        'date',
    ];

    // Relation: update belongs to a service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service');
    }
}
