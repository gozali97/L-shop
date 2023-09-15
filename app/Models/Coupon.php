<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'status',
        'coupons_name', 'start_date', 'end_date',
    ];

    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }

    public function discount($total)
    {
        if ($this->type == "fixed") {
            return $this->value;
        } elseif ($this->type == "percent") {
            return ($this->value / 100) * $total;
        } else {
            return 0;
        }
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_id', 'id');
    }

    public function currentStatus(): string
    {
        // If today is equal or greater than start date and the status is not active, then set status as active
        if (!is_null($this->start_date)) {
            if (date('Y-m-d') >= $this->start_date && $this->status !== 'active') {
                $this->status = 'active';
            }

            // If today is lesser than today and the status is active, then set status as inactive
            if (date('Y-m-d') < $this->start_date && $this->status === 'active') {
                $this->status = 'inactive';
            }
        }

        // If end date is greater than today and the status is still active, then set status as expired
        if (!is_null($this->end_date)) {
            if (date('Y-m-d') > $this->end_date && $this->status === 'active') {
                $this->status = 'expired';
            }
        }

        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->currentStatus() === 'active';
    }

    public function isInactive(): bool
    {
        return $this->currentStatus() === 'inactive';
    }

    public static function currentCoupons()
    {
        return self::withCount('orders')
            ->whereRaw("IF(`coupons`.start_date IS NULL, `coupons`.status = 'active', CURDATE() >= `coupons`.start_date) AND IF(`coupons`.end_date IS NULL, `coupons`.status = 'active', CURDATE() <= `coupons`.end_date)")
            ->orderBy('orders_count', 'DESC')
            ->get();
    }
}
