<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    // Table: payments (default) / PK: id (default)
    protected $fillable = [
        'client_id',
        'client_balance',
        'payment_amount',
        'payment_privatecode',
        'payment_method',
        'payment_status',
        'payment_delivery',
        'payment_note',
        'payment_mode',
        'payment_create_date',
        'payment_update_date',
        'payment_ip',
        'payment_extra',
        'payment_bank',
    ];

    protected $casts = [
        'client_id'            => 'integer',
        'client_balance'       => 'decimal:2',
        'payment_amount'       => 'decimal:4',
        'payment_method'       => 'integer',
        'payment_status'       => 'integer',
        'payment_delivery'     => 'integer',
        'payment_create_date'  => 'datetime',
        'payment_update_date'  => 'datetime',
    ];

    /**
     * FK: payments.client_id -> clients.client_id (non-standard owner key)
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    /**
     * Simple scopes for filtering in index()
     */
    public function scopeForClient($query, $clientId = null)
    {
        return $clientId ? $query->where('client_id', $clientId) : $query;
    }

    public function scopeStatus($query, $status = null)
    {
        return is_null($status) ? $query : $query->where('payment_status', $status);
    }

    public function scopeSearch($query, $term = null)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('payment_privatecode', 'like', "%{$term}%")
              ->orWhere('payment_note', 'like', "%{$term}%")
              ->orWhere('payment_mode', 'like', "%{$term}%")
              ->orWhere('payment_bank', 'like', "%{$term}%");
        });
    }
}
