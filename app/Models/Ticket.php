<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'client_id',
        'subject',
        'status',        // string, default 'pending'
        'client_new',    // '1' or '2'
        'support_new',   // '1' or '2'
        'canmessage',    // '1' or '2'
    ];

    protected $casts = [
        'client_new'  => 'string',
        'support_new' => 'string',
        'canmessage'  => 'string',
    ];

    /** Scope: filter by status string */
    public function scopeStatus(Builder $q, string $status): Builder
    {
        return $q->where('status', $status);
    }

    /** Scope: filter by client id */
    public function scopeForClient(Builder $q, int $clientId): Builder
    {
        return $q->where('client_id', $clientId);
    }
}
