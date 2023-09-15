<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Bank;
use App\Models\Order;
use App\Models\Settings;
use App\Models\Shipping;
use App\Models\ShippingFee;
use App\Models\StoreAddress;
use App\Notifications\StatusNotification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Helper;

class CartController extends Controller
{
    protected $product = null;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }


    public function index()
    {

        if (\Auth::user()->id) {
            $user_id =  \Auth::user()->id;
            $products = Cart::query()
                ->join('products', 'products.id', 'carts.product_id')
                ->select('carts.*', 'products.title', 'products.slug', 'products.summary', 'products.description', 'products.photo', 'products.price')
                ->where('user_id', $user_id)->where('order_id', null)->get();
        } else {
            $products = 0;
        }

        $photoProduct = [];

        foreach ($products as $p) {
            $cmd = \Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $p->photo,
            ]);

            $presignedRequest = \Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoProduct[] = $preSignedUrl;
        }
        return view('frontend.pages.cart')
            ->with('products', $products)
            ->with('photoProduct', $photoProduct);
    }

    public function addToCart(Request $request)
    {
        // dd($request->all());
        if (empty($request->slug)) {
            request()->session()->flash('error', 'Invalid Products');
            return back();
        }
        $product = Product::where('slug', $request->slug)->first();
        // return $product;
        if (empty($product)) {
            request()->session()->flash('error', 'Invalid Products');
            return back();
        }

        $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->where('product_id', $product->id)->first();
        // return $already_cart;
        if ($already_cart) {
            // dd($already_cart);
            $already_cart->quantity = $already_cart->quantity + 1;
            $already_cart->amount = $product->price + $already_cart->amount;
            // return $already_cart->quantity;
            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) return back()->with('error', 'Stock not sufficient!.');
            $already_cart->save();
        } else {

            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity = 1;
            $cart->amount = $cart->price * $cart->quantity;
            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error', 'Stock not sufficient!.');
            $cart->save();
            $wishlist = Wishlist::where('user_id', auth()->user()->id)->where('cart_id', null)->update(['cart_id' => $cart->id]);
        }
        request()->session()->flash('success', 'Product successfully added to cart');
        return back();
    }

    public function singleAddToCart(Request $request)
    {
        $request->validate([
            'slug'      =>  'required',
            'quant'      =>  'required',
        ]);
        // dd($request->quant[1]);


        $product = Product::where('slug', $request->slug)->first();
        if ($product->stock < $request->quant[1]) {
            return back()->with('error', 'Out of stock, You can add other products.');
        }
        if (($request->quant[1] < 1) || empty($product)) {
            request()->session()->flash('error', 'Invalid Products');
            return back();
        }

        $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->where('product_id', $product->id)->first();

        // return $already_cart;

        if ($already_cart) {
            $already_cart->quantity = $already_cart->quantity + $request->quant[1];
            // $already_cart->price = ($product->price * $request->quant[1]) + $already_cart->price ;
            $already_cart->amount = ($product->price * $request->quant[1]) + $already_cart->amount;

            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) return back()->with('error', 'Stock not sufficient!.');

            $already_cart->save();
        } else {

            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->product_id = $product->id;
            $cart->price = ($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity = $request->quant[1];
            $cart->amount = ($product->price * $request->quant[1]);
            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) return back()->with('error', 'Stock not sufficient!.');
            // return $cart;
            $cart->save();
        }
        request()->session()->flash('success', 'Product successfully added to cart.');
        return back();
    }

    public function cartDelete(Request $request)
    {
        $cart = Cart::find($request->id);
        if ($cart) {
            $cart->delete();
            request()->session()->flash('success', 'Cart successfully removed');
            return back();
        }
        request()->session()->flash('error', 'Error please try again');
        return back();
    }

    public function cartUpdate(Request $request)
    {
        // dd($request->all());
        if ($request->quant) {
            $error = array();
            $success = '';
            // return $request->quant;
            foreach ($request->quant as $k => $quant) {
                // return $k;
                $id = $request->qty_id[$k];
                // return $id;
                $cart = Cart::find($id);
                // return $cart;
                if ($quant > 0 && $cart) {
                    // return $quant;

                    if ($cart->product->stock < $quant) {
                        request()->session()->flash('error', 'Out of stock');
                        return back();
                    }
                    $cart->quantity = ($cart->product->stock > $quant) ? $quant  : $cart->product->stock;
                    // return $cart;

                    if ($cart->product->stock <= 0) continue;
                    $after_price = ($cart->product->price - ($cart->product->price * $cart->product->discount) / 100);
                    $cart->amount = $after_price * $quant;
                    // return $cart->price;
                    $cart->save();
                    $success = 'Cart successfully updated!';
                } else {
                    $error[] = 'Cart Invalid!';
                }
            }
            return back()->with($error)->with('success', $success);
        } else {
            return back()->with('Cart Invalid!');
        }
    }

    public function checkout(Request $request)
    {

        $id = \Auth::user()->id;
        $store = StoreAddress::find(1);
        $address = Address::where('user_id', $id)->where('delivery_instructions', 'primary')->first();
        $products = Cart::query()
            ->join('products', 'products.id', 'carts.product_id')
            ->select('carts.*', 'products.title', 'products.slug', 'products.summary', 'products.description', 'products.photo', 'products.price')
            ->where('user_id', $id)->where('order_id', null)->get();
        $photoProduct = [];

        foreach ($products as $p) {
            $cmd = \Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $p->photo,
            ]);

            $presignedRequest = \Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoProduct[] = $preSignedUrl;
        }

        $bankAccounts = Bank::all();

        $shippingCosts = '';
        if ($address) {
            $fee = ShippingFee::find(1);
            $courir = json_decode($fee->courir, true);

            $availableCourir = array_keys(array_filter($courir));

            $shippingCosts = [];
            $alreadyCart =  Cart::query()
                ->join('products', 'products.id', 'carts.product_id')
                ->where('user_id', $id)
                ->where('order_id', null)
                ->get();

            $weight = 0;

            foreach ($alreadyCart as $ready) {

                $weight += $ready->weight * $ready->quantity;
            }

            foreach ($availableCourir as $courier) {
                $response = Http::withHeaders([
                    'key' => env('RAJAONGKIR_API_KEY'),
                ])->post('https://pro.rajaongkir.com/api/cost', [
                    'origin' => $store->district_id,
                    'originType' => 'subdistrict',
                    'destination' => $address->district_id,
                    'destinationType' => 'subdistrict',
                    'weight' => $weight,
                    'courier' => $courier,
                ]);

                $costs = $response['rajaongkir']['results'];
                foreach ($costs as $cost) {
                    foreach ($cost['costs'] as $val) {

                        $price = 0;
                        if ($fee->amount_type === 'fixed') {
                            $price = $val['cost'][0]['value'] + $fee->amount;
                        } else {
                            $discount = $fee->amount / 100;
                            $sum = $val['cost'][0]['value'] * $discount;
                            $price = $val['cost'][0]['value'] + $sum;
                        }

                        $shippingCosts[] = [
                            'name' => $cost['code'],
                            'type' => $val['service'],
                            'price' => $price,
                        ];
                    }
                }
            }
        }

        $province =  Http::withHeaders([
            'key' => '067f3c3070f9ba9652054f7f1eb0e182',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get('https://api.rajaongkir.com/starter/province');

        $dataProvince = $province['rajaongkir']['results'];

        return view('frontend.pages.checkout', compact('address', 'shippingCosts', 'dataProvince', 'photoProduct', 'bankAccounts'));
    }

    public function getCost(Request $request)
    {

        $destination = $request->destination;

        $store = StoreAddress::find(1);
        $fee = ShippingFee::find(1);
        $courir = json_decode($fee->courir, true);

        $availableCourir = array_keys(array_filter($courir));

        $shippingCosts = [];

        $alreadyCart =  Cart::query()
            ->join('products', 'products.id', 'carts.product_id')
            ->where('user_id', auth()->user()->id)
            ->where('order_id', null)
            ->get();

        $weight = 0;

        foreach ($alreadyCart as $ready) {
            $weight += $ready->weight * $ready->quantity;
        }

        foreach ($availableCourir as $courier) {
            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY'),
            ])->post('https://pro.rajaongkir.com/api/cost', [
                'origin' => $store->district_id,
                'originType' => 'subdistrict',
                'destination' => $destination,
                'destinationType' => 'subdistrict',
                'weight' => $weight,
                'courier' => $courier,
            ]);

            $costs = $response['rajaongkir']['results'];
            foreach ($costs as $cost) {
                foreach ($cost['costs'] as $val) {

                    $price = 0;
                    if ($fee->amount_type === 'fixed') {
                        $price = $val['cost'][0]['value'] + $fee->amount;
                    } else {
                        $discount = $fee->amount / 100;
                        $sum = $val['cost'][0]['value'] * $discount;
                        $price = $val['cost'][0]['value'] + $sum;
                    }

                    $shippingCosts[] = [
                        'name' => $cost['code'],
                        'type' => $val['service'],
                        'price' => $price,
                    ];
                }
            }
        }
        return response()->json(['cost' => $shippingCosts]);
    }

    public function getCity(Request $request)
    {
        $prov_id = $request->prov_id;

        $response = Http::withHeaders([
            'key' => '067f3c3070f9ba9652054f7f1eb0e182',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get('https://api.rajaongkir.com/starter/city?province=' . $prov_id);

        $dataCities = $response['rajaongkir']['results'];

        return response()->json(['data' => $dataCities]);
    }

    public function getDistrict(Request $request)
    {
        $city_id = $request->city_id;

        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get('https://pro.rajaongkir.com/api/subdistrict?city=' . $city_id);

        $dataCities = $response['rajaongkir']['results'];

        return response()->json(['data' => $dataCities]);
    }

    /**
     * Store cart data and create an order.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            $validasi = Validator::make($request->all(), [
                'firstname' => 'required',
                'email' => 'required',
                'lastname' => 'required',
                'phone' => 'required',
                'address1' => 'required',
                'postcode' => 'required',
            ]);

            if ($validasi->fails()) {
                return back()->withErrors($validasi)->withInput();
            }

            $user_id = \Illuminate\Support\Facades\Auth::user()->id;

            $alreadyHaveAddress = Address::where('user_id', $user_id)->first();

            if (!$alreadyHaveAddress) {
                Address::create([
                    'user_id' => $user_id,
                    'country_id' => 360,
                    'first_name' => $request->firstname,
                    'last_name' => $request->lastname,
                    'contact_email' => $request->email,
                    'contact_phone' => $request->phone,
                    'state' => $request->province,
                    'city' => $request->city,
                    'district' => $request->district,
                    'district_id' => $request->district_id,
                    'address' => $request->address1,
                    'address2' => $request->address2,
                    'postcode' => $request->postcode,
                    'delivery_instructions' => 'primary',
                ]);
            }

            if (empty(Cart::where('user_id', auth()->user()->id)->where('order_id', null)->first())) {
                request()->session()->flash('error', 'Cart is Empty !');
                return back();
            }
            $shippingInfo = $request->shipping;
            $shippingParts = explode('-', $shippingInfo);

            $type = $shippingParts[0];

            if ($shippingParts[0] == 'J&T') {
                $type = 'jnt';
            }

            $shipping = new Shipping;
            $shipping->type = $type;
            $shipping->service = $shippingParts[1];
            $shipping->price = (int)$shippingParts[2];
            $shipping->status = 'new';

            if ($shipping->save()) {

                $order = new Order();
                $order_data['user_id'] = $user_id;
                $order_data['country'] = 360;
                $order_data['first_name'] = $request->firstname;
                $order_data['last_name'] = $request->lastname;
                $order_data['email'] = $request->email;
                $order_data['phone'] = $request->phone;
                $order_data['address1'] = $request->address1;
                $order_data['address2'] = $request->address2;
                $order_data['post_code'] = $request->postcode;
                $order_data['order_number'] = 'ORD-' . strtoupper(Str::random(10));
                $order_data['user_id'] = $request->user()->id;
                $order_data['shipping_id'] = $shipping->id;
                $order_data['payment_bill'] = '';

                $order_data['sub_total'] = Helper::totalCartPrice();
                $order_data['quantity'] = Helper::cartCount();

                if (session('coupon')) {
                    $order_data['coupon'] = session('coupon')['value'];
                    $order_data['coupon_id'] = session('coupon')['id'];
                }
                if ($request->shipping) {
                    if (session('coupon')) {
                        $order_data['total_amount'] = Helper::totalCartPrice() + (int)$shippingParts[2] - session('coupon')['value'];
                    } else {
                        $order_data['total_amount'] = Helper::totalCartPrice() + (int)$shippingParts[2];
                    }
                } else {
                    if (session('coupon')) {
                        $order_data['total_amount'] = Helper::totalCartPrice() - session('coupon')['value'];
                    } else {
                        $order_data['total_amount'] = Helper::totalCartPrice();
                    }
                }

                $order_data['status'] = "pending";
                if (request('payment_method') == 'paypal') {
                    $order_data['payment_method'] = 'paypal';
                    $order_data['payment_status'] = 'paid';
                } else {
                    $order_data['payment_method'] = 'bank-transfer';
                    $order_data['payment_status'] = 'Unpaid';
                    $order_data['payment_bank_id'] = request('payment_method');
                }
                $order->fill($order_data);
                $status = $order->save();
                if ($order)

                    $users = User::where('role', 'admin')->first();
                $details = [
                    'title' => 'New order created',
                    'actionURL' => route('order.show', $order->id),
                    'fas' => 'fa-file-alt'
                ];
                Notification::send($users, new StatusNotification($details));
                if (request('payment_method') == 'paypal') {
                    return redirect()->route('payment')->with(['id' => $order->id]);
                } else {
                    session()->forget('cart');
                    session()->forget('coupon');
                }
                Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]);
                $cart = Cart::query()
                    ->join('products', 'products.id', 'carts.product_id')
                    ->where('order_id', $order->id)
                    ->get();


                foreach ($cart as $c) {
                    $photo = [];

                    foreach ($cart as $cr) {
                        $cmd = \Helper::s3()->getCommand('GetObject', [
                            'Bucket' => env('WASABI_BUCKET_NAME'),
                            'Key' => $cr->photo,
                        ]);

                        $presignedRequest = \Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
                        $preSignedUrl = (string)$presignedRequest->getUri();

                        $photo[] = $preSignedUrl;
                    }

                    foreach ($cart as $c) {
                        $product = Product::where('id', $c->product_id)->first();
                        $product->stock = $product->stock - $c->quantity;
                        $product->save();
                    }
                    $setting = Settings::find(1);
                    $address = Address::where('user_id', $order->user_id)->first();

                    $brandLogo = null;

                    if ($setting->brand_logo) {
                        $cmd = \Helper::s3()->getCommand('GetObject', [
                            'Bucket' => env('WASABI_BUCKET_NAME'),
                            'Key' => $setting->brand_logo,
                        ]);
                        $presignedRequest = \Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
                        $brandLogo = (string)$presignedRequest->getUri();
                    }

                    $emaildata = [
                        'order' => $order,
                        'cart' => $cart,
                        'photo' => $photo,
                        'shipping' => $shipping,
                        'settings' => $setting,
                        'address' => $address,
                        'brandLogo' => $brandLogo,
                        'email' => $order->email,
                        'subject' => 'Pesanan #' . $order->order_number . ' telah dikonfirmasi',
                    ];

                    Mail::send('email.email-order-confirm', $emaildata, function ($message) use ($emaildata) {
                        $message->to($emaildata['email'])->subject($emaildata['subject']);
                    });
                }

                DB::commit();
                request()->session()->flash('success', 'Your product successfully placed in order');
                //                return redirect()->route('home');
                return redirect()->route('user.order.show', $order->id);
            }
        } catch (\Exception $e) {

            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }
}













// public function addToCart(Request $request){
//     // return $request->all();
//     if(Auth::check()){
//         $qty=$request->quantity;
//         $this->product=$this->product->find($request->pro_id);
//         if($this->product->stock < $qty){
//             return response(['status'=>false,'msg'=>'Out of stock','data'=>null]);
//         }
//         if(!$this->product){
//             return response(['status'=>false,'msg'=>'Product not found','data'=>null]);
//         }
//         // $session_id=session('cart')['session_id'];
//         // if(empty($session_id)){
//         //     $session_id=Str::random(30);
//         //     // dd($session_id);
//         //     session()->put('session_id',$session_id);
//         // }
//         $current_item=array(
//             'user_id'=>auth()->user()->id,
//             'id'=>$this->product->id,
//             // 'session_id'=>$session_id,
//             'title'=>$this->product->title,
//             'summary'=>$this->product->summary,
//             'link'=>route('product-detail',$this->product->slug),
//             'price'=>$this->product->price,
//             'photo'=>$this->product->photo,
//         );

//         $price=$this->product->price;
//         if($this->product->discount){
//             $price=($price-($price*$this->product->discount)/100);
//         }
//         $current_item['price']=$price;

//         $cart=session('cart') ? session('cart') : null;

//         if($cart){
//             // if anyone alreay order products
//             $index=null;
//             foreach($cart as $key=>$value){
//                 if($value['id']==$this->product->id){
//                     $index=$key;
//                 break;
//                 }
//             }
//             if($index!==null){
//                 $cart[$index]['quantity']=$qty;
//                 $cart[$index]['amount']=ceil($qty*$price);
//                 if($cart[$index]['quantity']<=0){
//                     unset($cart[$index]);
//                 }
//             }
//             else{
//                 $current_item['quantity']=$qty;
//                 $current_item['amount']=ceil($qty*$price);
//                 $cart[]=$current_item;
//             }
//         }
//         else{
//             $current_item['quantity']=$qty;
//             $current_item['amount']=ceil($qty*$price);
//             $cart[]=$current_item;
//         }

//         session()->put('cart',$cart);
//         return response(['status'=>true,'msg'=>'Cart successfully updated','data'=>$cart]);
//     }
//     else{
//         return response(['status'=>false,'msg'=>'You need to login first','data'=>null]);
//     }
// }

// public function removeCart(Request $request){
//     $index=$request->index;
//     // return $index;
//     $cart=session('cart');
//     unset($cart[$index]);
//     session()->put('cart',$cart);
//     return redirect()->back()->with('success','Successfully remove item');
// }




//public function checkout(Request $request){
////         $cart=session('cart');
////         $cart_index=\Str::random(10);
////         $sub_total=0;
////
////         dd($cart);
////         foreach($cart as $cart_item){
////             $sub_total+=$cart_item['amount'];
////             $data=array(
////                 'cart_id'=>$cart_index,
////                 'user_id'=>$request->user()->id,
////                 'product_id'=>$cart_item['id'],
////                 'quantity'=>$cart_item['quantity'],
////                 'amount'=>$cart_item['amount'],
////                 'status'=>'new',
////                 'price'=>$cart_item['price'],
////             );
////
////             $cart=new Cart();
////             $cart->fill($data);
////             $cart->save();
////         }
//
//    $province =  Http::withHeaders([
//        'key' => '067f3c3070f9ba9652054f7f1eb0e182',
//        'Accept' => 'application/json',
//        'Content-Type' => 'application/json',
//    ])->get('https://api.rajaongkir.com/starter/province');
//
//    $dataProvince = $province['rajaongkir']['results'];
//
//
//    dd($dataProvince);
//    return view('frontend.pages.checkout', compact('province'));
//}
