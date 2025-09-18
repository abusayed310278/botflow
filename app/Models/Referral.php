<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_client_id',
        'referral_clicks',
        'referral_sign_up',
        'referral_totalFunds_byRefered',
        'referral_earned_commision',
        'referral_requested_commision',
        'referral_total_commision',
        'referral_rejected_commision',
        'referral_status',
        'referral_code',
    ];

    protected $casts = [
        'referral_client_id'              => 'integer',
        'referral_clicks'                 => 'integer',
        'referral_sign_up'                => 'integer',
        'referral_totalFunds_byRefered'   => 'decimal:2',
        'referral_earned_commision'       => 'decimal:2',
        'referral_requested_commision'    => 'decimal:2',
        'referral_total_commision'        => 'decimal:2',
        'referral_rejected_commision'     => 'decimal:2',
        'referral_status'                 => 'boolean', // 0/1
    ];

    // FK: referrals.referral_client_id -> clients.client_id
    public function client()
    {
        return $this->belongsTo(Client::class, 'referral_client_id', 'client_id');
    }

    /** Quick scopes for filtering */
    public function scopeForClient($query, $clientId = null)
    {
        return $clientId ? $query->where('referral_client_id', $clientId) : $query;
    }

    public function scopeStatus($query, $status = null)
    {
        return is_null($status) ? $query : $query->where('referral_status', $status);
    }

    public function scopeSearch($query, $term = null)
    {
        if (!$term) return $query;
        return $query->where('referral_code', 'like', "%{$term}%");
    }
}
