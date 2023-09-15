<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Settings;
use App\Models\Shipping;
use App\Models\User;
use App\Models\UserBank;
use App\Notifications\StatusNotification;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Notification;
//use Barryvdh\DomPDF\Facade\Pdf;
use PDF;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $orders = Order::query()
        ->join('shippings', 'shippings.id','orders.shipping_id')
        ->select('orders.*', 'shippings.type', 'shippings.no_resi')
        ->orderBy('id', 'DESC')
            ->get();
//        dd($orders);
        return view('backend.order.index')->with('orders', $orders);
    }

    public function store(Request $request)
    {

        try{
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

            $user_id = Auth::user()->id;

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

            if($shippingParts[0] == 'J&T'){
                $type = 'jnt';
            }

            $shipping = new Shipping;
            $shipping->type = $type;
            $shipping->service = $shippingParts[1];
            $shipping->price = (int) $shippingParts[2];

            if($shipping->save()){

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

                $order_data['sub_total'] = Helper::totalCartPrice();
                $order_data['quantity'] = Helper::cartCount();

                if (session('coupon')) {
                    $order_data['coupon'] = session('coupon')['value'];
                }
                if ($request->shipping) {
                    if (session('coupon')) {
                        $order_data['total_amount'] = Helper::totalCartPrice() + (int) $shippingParts[2]- session('coupon')['value'];
                    } else {
                        $order_data['total_amount'] = Helper::totalCartPrice() + (int) $shippingParts[2];
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

                foreach ($cart as $c){
                    $product = Product::where('id', $c->product_id)->first();
                    $product->stock = $product->stock - $c->quantity;
                    $product->save();
                }

            }

            DB::commit();
            request()->session()->flash('success', 'Your product successfully placed in order');
            return redirect()->route('home');
        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $order = Order::query()
            ->join('banks', 'banks.id', 'orders.payment_bank_id')
            ->leftJoin('transactions', 'transactions.order_id', 'orders.id')
            ->leftJoin('refunds', 'refunds.order_id', 'orders.id')
            ->select('orders.*', 'banks.bank_name', 'banks.account_number','banks.branch_name','banks.account_name','transactions.transaction_name', 'transactions.transaction_value', 'transactions.transaction_bank','transactions.transaction_date','transactions.transaction_wa','transactions.transaction_file', 'refunds.reason', 'refunds.refund_request_file', 'refunds.amount as ref_amount','refunds.status as refund_status')
            ->where('orders.id', $id)
            ->first();
        $photo = null;

        if($order->payment_status == 'paid'){

            if ($order->transaction_file) {
                $cmd = Helper::s3()->getCommand('GetObject', [
                    'Bucket' => env('WASABI_BUCKET_NAME'),
                    'Key' => $order->transaction_file,
                ]);
                $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
                $photo = (string) $presignedRequest->getUri();
            }
        }

        $ref_file = null;

        if($order->status == 'refund-request'){

            if ($order->refund_request_file) {
                $ref = Helper::s3()->getCommand('GetObject', [
                    'Bucket' => env('WASABI_BUCKET_NAME'),
                    'Key' => $order->refund_request_file,
                ]);
                $presignedRequest = Helper::s3()->createPresignedRequest($ref, '+720 minutes');
                $ref_file = (string) $presignedRequest->getUri();
            }
        }

        $refund = Refund::query()
            ->join('shippings', 'shippings.id', 'refunds.shipping_id')
            ->join('user_banks', 'user_banks.user_id', 'refunds.user_id')
            ->select('refunds.*', 'shippings.no_resi', 'shippings.type', 'shippings.service', 'user_banks.account_number', 'user_banks.bank_name')
            ->where('refunds.order_id', $id)
            ->first();

            $refund_file = null;
        if($refund){
            if ($refund->refund_request_file) {
                $ref = Helper::s3()->getCommand('GetObject', [
                    'Bucket' => env('WASABI_BUCKET_NAME'),
                    'Key' => $refund->refund_request_file,
                ]);
                $presignedRequest = Helper::s3()->createPresignedRequest($ref, '+720 minutes');
                $refund_file = (string) $presignedRequest->getUri();
            }
        }

        // return $order;
        return view('backend.order.show')
            ->with('order', $order)
            ->with('photo', $photo)
            ->with('refund_file', $refund_file)
            ->with('refund', $refund)
            ->with('ref_file', $ref_file);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $order = Order::find($id);
        return view('backend.order.edit')->with('order', $order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        $this->validate($request, [
            'status' => 'required'
        ]);
        $data = $request->all();
        // return $request->status;
//        if ($request->status == 'delivered') {
//            foreach ($order->cart as $cart) {
//                $product = $cart->product;
//                // return $product;
//                $product->stock -= $cart->quantity;
//                $product->save();
//            }
//        }
        $status = $order->fill($data)->save();
        if ($status) {
            request()->session()->flash('success', 'Successfully updated order');
        } else {
            request()->session()->flash('error', 'Error while updating order');
        }
        return redirect()->route('order.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order) {
            $status = $order->delete();
            if ($status) {
                request()->session()->flash('success', 'Order Successfully deleted');
            } else {
                request()->session()->flash('error', 'Order can not deleted');
            }
            return redirect()->route('order.index');
        } else {
            request()->session()->flash('error', 'Order can not found');
            return redirect()->back();
        }
    }

    private function getBrandLogo()
    {
        $setting = Settings::find(1);

        if (!$setting->brand_logo) {
            return null;
        }

        $cmd = Helper::s3()->getCommand('GetObject', [
            'Bucket' => env('WASABI_BUCKET_NAME'),
            'Key' => $setting->brand_logo,
        ]);

        $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');

        return (string) $presignedRequest->getUri();
    }

    public function addResi(Request $request, $id)
    {
        $data = Shipping::find($id);
        $data->no_resi = $request->no_resi;

        if ($data->save()) {
            $order = Order::where('shipping_id', $id)->first();
            $order->status = 'shipping';
            $order->save();

            $cart = Cart::query()
                ->join('products', 'products.id', 'carts.product_id')
                ->where('order_id', $order->id)
                ->get();
            $setting = Settings::find(1);

            $address = Address::where('user_id', $order->user_id)->first();

            $emaildata = [
                'order'       => $order,
                'cart'       => $cart,
                'brandLogo'       => $this->getBrandLogo(),
                'settings'      => $setting,
                'data'      => $data,
                'address'      => $address,
                'email'         => $order->email,
                'subject'       => 'Pesanan #' . $order->order_number. ' dalam pengiriman',
            ];

            Mail::send('email.email-order-send-waybill', $emaildata, function ($message) use ($emaildata) {
                $message->to($emaildata['email'])->subject($emaildata['subject']);
            });

            request()->session()->flash('success', 'Successfully add no resi');
        } else {
            request()->session()->flash('error', 'Error while add no resi');
        }
        return redirect()->back();
    }

//    public function confirm($id)
//    {
//        $order = Order::query()
//            ->join('shippings', 'shippings.id','orders.shipping_id')
//            ->select('orders.*', 'shippings.type', 'shippings.no_resi')
//            ->where('orders.id', $id)
//            ->first();
//
//        return view('backend.order.confirm', compact('order'));
//    }
    public function confirmStore(Request $request, $id)
    {
        try{

            DB::beginTransaction();
            $order = Order::find($id);

            if ($request->status == 'pending'){
                $order->status = $request->status;
                $order->note = $request->note;
                $order->payment_status = 'unpaid';
                $order->save();

                $cart = Cart::query()
                    ->join('products', 'products.id', 'carts.product_id')
                    ->where('order_id', $order->id)
                    ->get();

                $setting = Settings::find(1);

                $address = Address::where('user_id', $order->user_id)->first();

                $emaildata = [
                    'order'       => $order,
                    'note'       => $request->note,
                    'cart'       => $cart,
                    'brandLogo'       => $this->getBrandLogo(),
                    'settings'      => $setting,
                    'address'      => $address,
                    'email'         => $order->email,
                    'subject'       => 'Pembayaran pesanan #' . $order->order_number. ' ditolak',
                ];

                Mail::send('email.email-order-reject', $emaildata, function ($message) use ($emaildata) {
                    $message->to($emaildata['email'])->subject($emaildata['subject']);
                });

            }else{
                $order->status = $request->status;
                $order->save();

                $cart = Cart::query()
                    ->join('products', 'products.id', 'carts.product_id')
                    ->where('order_id', $order->id)
                    ->get();
                $setting = Settings::find(1);

                $address = Address::where('user_id', $order->user_id)->first();

                $emaildata = [
                    'order'       => $order,
                    'cart'       => $cart,
                    'brandLogo'       => $this->getBrandLogo(),
                    'settings'      => $setting,
                    'address'      => $address,
                    'email'         => $order->email,
                    'subject'       => 'Pesanan #' . $order->order_number. ' has been processed',
                ];

                Mail::send('email.email-order-process', $emaildata, function ($message) use ($emaildata) {
                    $message->to($emaildata['email'])->subject($emaildata['subject']);
                });
            }

            DB::commit();
            request()->session()->flash('success', 'Successfull confirm order payment ');
            return redirect()->route('order.index');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }

    public function confirmRefund(Request $request, $id)
    {
        try{

            DB::beginTransaction();
            $order = Order::find($id);
            $refund = Refund::where('order_id', $id)->first();

            if ($request->status2 == 'refund-rejected'){
                $order->status = $request->status2;
                $order->save();

                $refund->status = 'rejected';
                $refund->save();

                $cart = Cart::query()
                    ->join('products', 'products.id', 'carts.product_id')
                    ->where('order_id', $order->id)
                    ->get();

                $setting = Settings::find(1);

                $address = Address::where('user_id', $order->user_id)->first();

                $emaildata = [
                    'order'       => $order,
                    'note'       => $request->note,
                    'cart'       => $cart,
                    'brandLogo'       => $this->getBrandLogo(),
                    'settings'      => $setting,
                    'address'      => $address,
                    'email'         => $order->email,
                    'subject'       => 'Your Refund order #' . $order->order_number. ' rejected',
                ];

                Mail::send('email.email-refund-reject', $emaildata, function ($message) use ($emaildata) {
                    $message->to($emaildata['email'])->subject($emaildata['subject']);
                });

            }else{
                $order->status = $request->status2;
                $order->save();

                $refund->status = 'processed';
                $refund->save();

                $cart = Cart::query()
                    ->join('products', 'products.id', 'carts.product_id')
                    ->where('order_id', $order->id)
                    ->get();
                $setting = Settings::find(1);

                $address = Address::where('user_id', $order->user_id)->first();

                $emaildata = [
                    'order'       => $order,
                    'cart'       => $cart,
                    'brandLogo'       => $this->getBrandLogo(),
                    'settings'      => $setting,
                    'address'      => $address,
                    'email'         => $order->email,
                    'subject'       => 'Your Refund Order #' . $order->order_number. ' has accepted',
                ];

                Mail::send('email.email-refund-approve', $emaildata, function ($message) use ($emaildata) {
                    $message->to($emaildata['email'])->subject($emaildata['subject']);
                });
            }

            DB::commit();
            request()->session()->flash('success', 'Successfull confirm order payment ');
            return redirect()->route('order.index');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }

    public function refundFinish(Request $request, $id)
    {
        try{

            DB::beginTransaction();
            $order = Order::find($id);
            $refund = Refund::where('order_id', $id)->first();

            $order->status = 'refund-completed';
            $order->save();

            $refund->status = 'completed';
            if ($request->hasFile('refund_request_file')) {
                $image = $request->file('refund_request_file');

                $extension = $image->getClientOriginalExtension();
                $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
                $name = 'refund_receipt/' . $fileName;
                $result = \Helper::s3()->putObject([
                    'Bucket' => 'asima',
                    'Key' => $name,
                    'Body' => file_get_contents($image),
                    'ACL' => 'public-read',
                ]);

            }

            $refund->refund_receipt_file = $name;
            $refund->save();

            $user_bank = UserBank::where('user_id', $request->user_id)->first();
            $user_bank->save();

            $cart = Cart::query()
                ->join('products', 'products.id', 'carts.product_id')
                ->where('order_id', $order->id)
                ->get();

            $photo = [];

            foreach ($cart as $cr) {
                $cmd = Helper::s3()->getCommand('GetObject', [
                    'Bucket' => env('WASABI_BUCKET_NAME'),
                    'Key' => $cr->photo,
                ]);

                $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
                $preSignedUrl = (string) $presignedRequest->getUri();

                $photo[] = $preSignedUrl;
            }
            $setting = Settings::find(1);

            $address = Address::where('user_id', $order->user_id)->first();

            $emaildata = [
                'order'       => $order,
                'cart'       => $cart,
                'brandLogo'       => $this->getBrandLogo(),
                'settings'      => $setting,
                'address'      => $address,
                'bank'      => $user_bank,
                'photo'      => $photo,
                'amount'      => $request->amount,
                'email'         => $order->email,
                'subject'       => 'Your Refund Order #' . $order->order_number. ' Successfull',
            ];

            Mail::send('email.email-order-refund-finish', $emaildata, function ($message) use ($emaildata) {
                $message->to($emaildata['email'])->subject($emaildata['subject']);
            });

            DB::commit();
            request()->session()->flash('success', 'Successfull confirm order refund finish ');
            return redirect()->route('order.index');

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }

    public function viewTracking($id)
    {
        $order = Order::query()
        ->join('shippings', 'shippings.id','orders.shipping_id')
            ->select('orders.*', 'shippings.type', 'shippings.no_resi')
        ->where('orders.id', $id)
        ->first();

        try {
            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY'),
            ])->post('https://pro.rajaongkir.com/api/waybill', [
                'waybill' => $order->no_resi,
                'courier' => $order->type,
            ]);
                $tracking = $response['rajaongkir'];

            } catch (\Exception $e) {
                $tracking = 'Gagal terhubung ke server';
            }

            if($tracking['status']['code'] == 400){
                request()->session()->flash('error', $tracking['status']['description']);
                return back();
            }

        $shipping = Shipping::where('id', $order->shipping_id)->first();
        $shipping->status = $tracking['result']['summary']['status'];
        $shipping->save();

        return view('backend.order.tracking', compact('order','tracking'));
    }

    public function orderTrack()
    {
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request)
    {
        // return $request->all();
        if(isset(auth()->user()->id)){
            $order = Order::where('user_id', auth()->user()->id)->where('order_number', $request->order_number)->first();
            if ($order) {
                if ($order->status == "new") {
                    request()->session()->flash('success', 'Your order has been placed. please wait.');
                    return redirect()->route('home');
                } elseif ($order->status == "process") {
                    request()->session()->flash('success', 'Your order is under processing please wait.');
                    return redirect()->route('home');
                } elseif ($order->status == "delivered") {
                    request()->session()->flash('success', 'Your order is successfully delivered.');
                    return redirect()->route('home');
                } else {
                    request()->session()->flash('error', 'Your order canceled. please try again');
                    return redirect()->route('home');
                }
            } else {
                request()->session()->flash('error', 'Invalid order numer please try again');
                return back();
            }
        }else{
            request()->session()->flash('error', 'You must login first');
            return redirect()->route('login');
        }

    }

    // PDF generate
    public function pdf(Request $request)
    {
        $order = Order::getAllOrder($request->id);
        // return $order;
        $file_name = $order->order_number . '-' . $order->first_name . '.pdf';
        // return $file_name;
        $pdf = PDF::loadview('backend.order.pdf', compact('order'));
        return $pdf->download($file_name);
    }

    public function invoice($id)
    {
        $order = Order::query()
                ->join('shippings', 'shippings.id', 'orders.shipping_id')
                ->join('banks', 'banks.id', 'orders.payment_bank_id')
                ->leftJoin('coupons', 'coupons.id', 'orders.coupon')
                ->select('orders.*', 'shippings.price as price_shipping', 'shippings.type', 'shippings.service', 'coupons.value as price_coupon', 'banks.bank_name', 'banks.account_number')
                ->where('orders.id', $id)
                ->first();

        $product = Cart::query()
            ->join('products', 'products.id', 'carts.product_id')
            ->where('carts.order_id', $id)
            ->get();

        return view('print.invoice', compact('order', 'product'));
    }
    // Income chart
    public function incomeChart(Request $request)
    {
        $year = \Carbon\Carbon::now()->year;


        $items = Order::with(['cart_info'])->whereYear('created_at', $year)->where('status', 'completed')->get()
            ->groupBy(function ($d) {
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });


        $result = [];
        foreach ($items as $month => $item_collections) {
            foreach ($item_collections as $item) {
                $amount = $item->cart_info->sum('amount');

                $m = intval($month);
                // return $m;
                isset($result[$m]) ? $result[$m] += $amount : $result[$m] = $amount;
            }
        }
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $data[$monthName] = (!empty($result[$i])) ? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }

    /**
     * Generate item list for shipping
     *
     * @param Request $request
     * @return View
     */
    public function itemListPdf(Request $request): View
    {
        $order = Order::getAllOrder($request->id);
        if (!$order) {
            abort(404);
        }
        $items = Cart::with(['product'])->where('order_id', $order->id)->get();
        $weight = 0;
        foreach ($items as $item) $weight += ($item->quantity * $item->product->weight);

        return view('backend.order.item-list-pdf', compact('order', 'items', 'weight'));
    }
}
