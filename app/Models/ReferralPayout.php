<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferralPayout extends Model
{
    use HasFactory;

    protected $table = 'referrals_payouts';

    protected $fillable = [
        'client_id',
        'code',
        'amount_requested',
        'status',
    ];

    protected $casts = [
        'client_id'        => 'integer',
        'amount_requested' => 'decimal:2',
    ];

    // FK: referrals_payouts.client_id -> clients.client_id
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    // Convenience for lists (to show "Username" without JOIN in views)
    protected $appends = ['username'];

    public function getUsernameAttribute(): ?string
    {
        return $this->client->username ?? null;
    }
}
