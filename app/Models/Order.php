<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $guarded = ['id'];

    public function cart_info(){
        return $this->hasMany('App\Models\Cart','order_id','id');
    }
    public static function getAllOrder($id){
        return Order::with('cart_info')->find($id);
    }
    public static function countActiveOrder(){
        $data=Order::count();
        if($data){
            return $data;
        }
        return 0;
    }
    public function cart(){
        return $this->hasMany(Cart::class);
    }

    public function shipping(){
        return $this->belongsTo(Shipping::class,'shipping_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function canConfirmPayment(): bool
    {
        return in_array($this->status, ['payment-confirm-request', 'pending']);
    }

    public function canAddWaybill(): bool
    {
        return $this->status === 'processing';
    }

    public static function recentOrders(int $limit = 4)
    {
        return self::orderBy('id', 'DESC')->limit($limit)->get();
    }

}
