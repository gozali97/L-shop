<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasFactory, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'email_verified_at', 'role', 'photo', 'status', 'provider', 'provider_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    public static function recentSignUps(int $limit = 4)
    {
        return self::where('role', 'user')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function topCustomers(int $limit = 4)
    {
        return self::withCount('orders')
            ->where('role', 'user')
            ->withSum('orders', 'total_amount')
            ->orderBy('orders_count', 'DESC')
            ->limit($limit)
            ->get();
    }
}
