<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'year', 'month',
        'category_id', 'service_id', 'payment_id',   // <-- changed
        'orders_count', 'quantity_sum',
        'gross_amount', 'cost_amount', 'refund_amount', 'fee_amount',
        'net_amount', 'profit_amount',
    ];

    protected $casts = [
        'year'           => 'integer',
        'month'          => 'integer',
        'orders_count'   => 'integer',
        'quantity_sum'   => 'decimal:4',
        'gross_amount'   => 'decimal:2',
        'cost_amount'    => 'decimal:2',
        'refund_amount'  => 'decimal:2',
        'fee_amount'     => 'decimal:2',
        'net_amount'     => 'decimal:2',
        'profit_amount'  => 'decimal:2',
    ];

    public function scopeYear(Builder $q, int $year): Builder { return $q->where('year', $year); }
    public function scopeMonth(Builder $q, ?int $month): Builder
    {
        return $month === null ? $q->whereNull('month') : $q->where('month', $month);
    }
}
