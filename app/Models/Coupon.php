<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'piece',
        'amount',
    ];

    protected $casts = [
        'piece'  => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /** Search by (partial) code */
    public function scopeCodeLike(Builder $q, string $needle): Builder
    {
        return $q->where('code', 'like', '%' . $needle . '%');
    }

    /** Piece range filter */
    public function scopePieceBetween(Builder $q, float $min, float $max): Builder
    {
        return $q->whereBetween('piece', [$min, $max]);
    }
}
