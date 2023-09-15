<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Bank;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Shipping;
use App\Models\ShippingFee;
use App\Models\Transactions;
use App\Models\PostComment;
use App\Models\ProductReview;
use App\Models\Settings;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Hash;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index()
    {
        return view('user.index');
    }

    public function profile()
    {
        $profile = Auth()->user();
        // return $profile;
        return view('user.users.profile')->with('profile', $profile);
    }

    public function profileUpdate(Request $request, $id)
    {
        // return $request->all();
        $user = User::findOrFail($id);
        $data = $request->all();
        $status = $user->fill($data)->save();
        if ($status) {
            request()->session()->flash('success', 'Successfully updated your profile');
        } else {
            request()->session()->flash('error', 'Please try again!');
        }
        return redirect()->back();
    }

    // Order
    public function orderIndex()
    {
        $orders = Order::query()
            ->join('shippings', 'shippings.id','orders.shipping_id')
            ->leftJoin('refunds', 'refunds.order_id','orders.id')
            ->select('orders.*', 'shippings.type', 'shippings.no_resi', 'refunds.shipping_id as refund_shipping', 'refunds.status as refund_status')
            ->where('orders.user_id', auth()->user()->id)
            ->orderBy('orders.id', 'DESC')
            ->get();

        return view('user.order.index')->with('orders', $orders);
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

        return view('user.order.tracking', compact('order','tracking'));
    }

    public function userOrderDelete($id)
    {
        $order = Order::find($id);
        if ($order) {
            if ($order->status == "process" || $order->status == 'delivered' || $order->status == 'cancel') {
                return redirect()->back()->with('error', 'You can not delete this order now');
            } else {
                $status = $order->delete();
                if ($status) {
                    request()->session()->flash('success', 'Order Successfully deleted');
                } else {
                    request()->session()->flash('error', 'Order can not deleted');
                }
                return redirect()->route('user.order.index');
            }
        } else {
            request()->session()->flash('error', 'Order can not found');
            return redirect()->back();
        }
    }

    public function confirm($id)
    {
        $order = Order::find($id);
        $bank = Bank::where('status', 'active')->get();

        return view('user.order.confirm', compact('order', 'bank'));
    }

    private function getBrandLogoUrl() {
        $brandLogo = null;

        $setting = Settings::find(1);
        if ($setting->brand_logo) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $setting->brand_logo,
            ]);
            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $brandLogo = (string) $presignedRequest->getUri();
        }

        return $brandLogo;
    }

    public function confirmStore(Request $request, $id)
    {
        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Mengambil order dengan ID yang sesuai
            $order = Order::find($id);

            $bank = Bank::find($order->payment_bank_id);

            // Validasi input
            $validasi = Validator::make($request->all(), [
                'transaction_name' => 'required',
                'transaction_value' => 'required',
                'transaction_bank' => 'required',
                'transaction_date' => 'required',
                'transaction_wa' => 'required',
                'transaction_file' => 'required',
            ]);

            if ($validasi->fails()) {
                return back()->withErrors($validasi)->withInput();
            }

            // Membuat instance transaction
            $confirm = new Transactions;

            // Memeriksa apakah ada file gambar yang diunggah
            if ($request->hasFile('transaction_file')) {
                $image = $request->file('transaction_file');

                $extension = $image->getClientOriginalExtension();
                $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
                $name = 'payment_confirmation/' . $fileName;

                // Upload gambar ke Wasabi
                $result = Helper::s3()->putObject([
                    'Bucket' => env('WASABI_BUCKET_NAME'),
                    'Key' => $name,
                    'Body' => file_get_contents($image),
                    'ACL' => 'public-read',
                ]);

                $confirm->transaction_file = $name;
            }

            // Mengisi data untuk Transactions
            $confirm->order_id = $request->order_id;
            $confirm->transaction_name = $request->transaction_name;
            $confirm->transaction_value = $request->transaction_value;
            $confirm->transaction_bank = $request->transaction_bank;
            $confirm->transaction_date = $request->transaction_date;
            $confirm->transaction_wa = $request->transaction_wa;
            $confirm->save();

            // Mengubah status pembayaran dan pesanan
            $order->payment_status = 'paid';
            $order->status = 'payment-confirm-request';
            $order->save();

            // Mengambil data keranjang
            $cart = Cart::query()
                ->join('products', 'products.id', 'carts.product_id')
                ->where('order_id', $order->id)
                ->get();

            // Mengambil pengaturan
            $setting = Settings::find(1);

            // Mengambil alamat pengiriman
            $address = Address::where('user_id', $order->user_id)->first();

            // Membuat data untuk email
            $emaildata = [
                'order' => $order,
                'cart' => $cart,
                'settings' => $setting,
                'brandLogo' => $this->getBrandLogoUrl(),
                'address' => $address,
                'email' => $order->email,
                'subject' => 'Payment order #' . $order->order_number . ' has been confirmed',
            ];

            // Mengirim email costumer
            Mail::send('email.email-payment-confirm', $emaildata, function ($message) use ($emaildata) {
                $message->to($emaildata['email'])->subject($emaildata['subject']);
            });

            $emailAdmin = [
                'order' => $order,
                'cart' => $cart,
                'settings' => $setting,
                'brandLogo' => $this->getBrandLogoUrl(),
                'address' => $address,
                'email' => $setting->email,
                'subject' => 'Payment order #' . $order->order_number . ' has been received',
            ];
            // Mengirim email admin
            Mail::send('email.email-payment-confirm-admin', $emailAdmin, function ($message) use ($emailAdmin) {
                $message->to($emailAdmin['email'])->subject($emailAdmin['subject']);
            });

            // Commit transaksi database
            DB::commit();

            // Redirect dengan pesan sukses
            request()->session()->flash('success', 'Successfully Upload Proof Of Payment ');
            return redirect()->route('user.order.index');
        } catch (\Exception $e) {
            // Rollback transaksi database jika terjadi kesalahan
            DB::rollback();
            // Menampilkan pesan kesalahan jika diperlukan
            echo $e->getMessage();

            // Redirect dengan pesan kesalahan
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
            return redirect()->back()->withInput();
        }
    }

    public function confirmCompleted(Request $request, $id)
    {

        try{

            DB::beginTransaction();
            $order = Order::find($id);
            $order->status = 'completed';
            $order->save();

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
                'photo'       => $photo,
                'settings'      => $setting,
                'address'      => $address,
                'brandLogo'      => $this->getBrandLogoUrl(),
                'email'         => $order->email,
                'subject'       => 'Pesanan #' . $order->order_number. ' has been received',
            ];

            Mail::send('email.email-order-completed', $emaildata, function ($message) use ($emaildata) {
                $message->to($emaildata['email'])->subject($emaildata['subject']);
            });

            DB::commit();
            // Redirect atau tampilkan pesan sukses
            request()->session()->flash('success', 'Your Order Has Completed');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }
    public function orderShow($id)
    {
        $order = Order::query()
        ->join('banks', 'banks.id', 'orders.payment_bank_id')
        ->leftJoin('transactions', 'transactions.order_id', 'orders.id')
        ->leftJoin('refunds', 'refunds.order_id', 'orders.id')
        ->select('orders.*', 'banks.bank_name', 'banks.account_number','banks.branch_name','banks.account_name','transactions.transaction_name', 'transactions.transaction_value', 'transactions.transaction_bank','transactions.transaction_date','transactions.transaction_wa','transactions.transaction_file')
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

        return view('user.order.show')->with('order', $order)->with('photo', $photo);
    }

    public function refund($id)
    {
        $order = Order::query()
            ->join('banks', 'banks.id', 'orders.payment_bank_id')
            ->join('transactions', 'transactions.order_id', 'orders.id')
            ->select('orders.*', 'banks.bank_name', 'banks.account_number','banks.branch_name','banks.account_name','transactions.transaction_name', 'transactions.transaction_value', 'transactions.transaction_bank','transactions.transaction_date','transactions.transaction_wa','transactions.transaction_file')
            ->where('orders.id', $id)
            ->first();

        $product = Cart::query()
            ->join('products', 'products.id', 'carts.product_id')
            ->where('carts.order_id', $id)
            ->get();

        $photo = [];

            foreach ($product as $p) {
                $cmd = Helper::s3()->getCommand('GetObject', [
                    'Bucket' => env('WASABI_BUCKET_NAME'),
                    'Key' => $p->photo,
                ]);
                $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
                $url = (string) $presignedRequest->getUri();
                $photo[] = $url;
            }

        return view('user.order.refund')->with('order', $order)->with('photo', $photo)->with('product', $product);
    }

    public function refundStore(Request $request, $id)
    {

        try{
            DB::beginTransaction();
            $order = Order::find($id);
            $order->status = 'refund-request';
            $order->save();

            $refund = new Refund;
            $refund->order_id = $id;
            $refund->user_id = Auth::user()->id;
            $refund->reason = $request->reason;
            $refund->amount = $request->amount;

            if ($request->hasFile('refund_request_file')) {
                $image = $request->file('refund_request_file');

                $extension = $image->getClientOriginalExtension();
                $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
                $name = 'refund_request/' . $fileName;
                // Upload gambar ke Wasabi
                $result = \Helper::s3()->putObject([
                    'Bucket' => 'asima',
                    'Key' => $name,
                    'Body' => file_get_contents($image),
                    'ACL' => 'public-read',
                ]);

            }
            $refund->refund_request_file = $name;

            if($refund->save()){

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
                    'photo'       => $photo,
                    'settings'      => $setting,
                    'address'      => $address,
                    'brandLogo'      => $this->getBrandLogoUrl(),
                    'email'         => $order->email,
                    'subject'       => 'Refund Order #' . $order->order_number. ' has been send',
                ];

                Mail::send('email.email-order-refund-request', $emaildata, function ($message) use ($emaildata) {
                    $message->to($emaildata['email'])->subject($emaildata['subject']);
                });

                $emailAdmin = [
                    'order' => $order,
                    'cart' => $cart,
                    'photo'       => $photo,
                    'settings' => $setting,
                    'brandLogo' => $this->getBrandLogoUrl(),
                    'address' => $address,
                    'email' => $setting->email,
                    'subject' => 'Refund order #' . $order->order_number . ' has been received',
                ];
                // Mengirim email admin
                Mail::send('email.email-order-refund-admin', $emailAdmin, function ($message) use ($emailAdmin) {
                    $message->to($emailAdmin['email'])->subject($emailAdmin['subject']);
                });
            }

            DB::commit();
            // Redirect atau tampilkan pesan sukses
            request()->session()->flash('success', 'Your Order Has Completed');
            return redirect()->route('user.order.index');

        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }

    public function refundShipping($id)
    {
        $order = Order::query()
            ->join('banks', 'banks.id', 'orders.payment_bank_id')
            ->join('transactions', 'transactions.order_id', 'orders.id')
            ->select('orders.*', 'banks.bank_name', 'banks.account_number','banks.branch_name','banks.account_name','transactions.transaction_name', 'transactions.transaction_value', 'transactions.transaction_bank','transactions.transaction_date','transactions.transaction_wa','transactions.transaction_file')
            ->where('orders.id', $id)
            ->first();

        $haveShipping = Refund::query()
            ->join('shippings', 'shippings.id', 'refunds.shipping_id')
            ->where('refunds.order_id', $id)
            ->where('shippings.status','refund-request')
            ->first();

        $product = Cart::query()
            ->join('products', 'products.id', 'carts.product_id')
            ->where('carts.order_id', $id)
            ->get();

        $courir = ShippingFee::find(1);
        $shipping = json_decode($courir->courir, true);

        return view('user.order.refund-shipping')
            ->with('order', $order)
            ->with('shipping', $shipping)
            ->with('haveShipping', $haveShipping)
            ->with('product', $product);
    }

    public function refundShippingStore(Request $request, $id)
    {

        try{
            DB::beginTransaction();
            $order = Order::find($id);

            $shipping = new Shipping;
            $shipping->type = $request->courir;
            $shipping->service = $request->service;
            $shipping->no_resi = $request->no_resi;
            $shipping->price = $request->price_shipping;
            $shipping->status = 'refund-request';

            if($shipping->save()){

                $refund = Refund::where('order_id', $id)->first();
                $refund->status = 'shipping';
                $refund->shipping_id = $shipping->id;
                $refund->save();

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
                    'photo'       => $photo,
                    'settings'      => $setting,
                    'address'      => $address,
                    'brandLogo'      => $this->getBrandLogoUrl(),
                    'email'         => $order->email,
                    'subject'       => 'Refund Waybill Order #' . $order->order_number. ' Has Been Send',
                ];

                Mail::send('email.email-order-refund-shipping', $emaildata, function ($message) use ($emaildata) {
                    $message->to($emaildata['email'])->subject($emaildata['subject']);
                });

                $emailAdmin = [
                    'order' => $order,
                    'cart' => $cart,
                    'shipping' => $shipping,
                    'photo'       => $photo,
                    'settings' => $setting,
                    'brandLogo' => $this->getBrandLogoUrl(),
                    'address' => $address,
                    'email' => $setting->email,
                    'subject' => 'Detail Shipping Refund Order #' . $order->order_number . ' Received',
                ];
                // Mengirim email admin
                Mail::send('email.email-order-refund-shipping-admin', $emailAdmin, function ($message) use ($emailAdmin) {
                    $message->to($emailAdmin['email'])->subject($emailAdmin['subject']);
                });
            }

            DB::commit();
            // Redirect atau tampilkan pesan sukses
            request()->session()->flash('success', 'Your Request Shipping Refund Has Completed');
            return redirect()->route('user.order.index');

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
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

    // Product Review
    public function productReviewIndex()
    {
        $reviews = ProductReview::getAllUserReview();
        return view('user.review.index')->with('reviews', $reviews);
    }

    public function productReviewEdit($id)
    {
        $review = ProductReview::find($id);
        // return $review;
        return view('user.review.edit')->with('review', $review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productReviewUpdate(Request $request, $id)
    {
        $review = ProductReview::find($id);
        if ($review) {
            $data = $request->all();
            $status = $review->fill($data)->update();
            if ($status) {
                request()->session()->flash('success', 'Review Successfully updated');
            } else {
                request()->session()->flash('error', 'Something went wrong! Please try again!!');
            }
        } else {
            request()->session()->flash('error', 'Review not found!!');
        }

        return redirect()->route('user.productreview.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function productReviewDelete($id)
    {
        $review = ProductReview::find($id);
        $status = $review->delete();
        if ($status) {
            request()->session()->flash('success', 'Successfully deleted review');
        } else {
            request()->session()->flash('error', 'Something went wrong! Try again');
        }
        return redirect()->route('user.productreview.index');
    }

    public function userComment()
    {
        $comments = PostComment::getAllUserComments();
        return view('user.comment.index')->with('comments', $comments);
    }
    public function userCommentDelete($id)
    {
        $comment = PostComment::find($id);
        if ($comment) {
            $status = $comment->delete();
            if ($status) {
                request()->session()->flash('success', 'Post Comment successfully deleted');
            } else {
                request()->session()->flash('error', 'Error occurred please try again');
            }
            return back();
        } else {
            request()->session()->flash('error', 'Post Comment not found');
            return redirect()->back();
        }
    }
    public function userCommentEdit($id)
    {
        $comments = PostComment::find($id);
        if ($comments) {
            return view('user.comment.edit')->with('comment', $comments);
        } else {
            request()->session()->flash('error', 'Comment not found');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userCommentUpdate(Request $request, $id)
    {
        $comment = PostComment::find($id);
        if ($comment) {
            $data = $request->all();
            // return $data;
            $status = $comment->fill($data)->update();
            if ($status) {
                request()->session()->flash('success', 'Comment successfully updated');
            } else {
                request()->session()->flash('error', 'Something went wrong! Please try again!!');
            }
            return redirect()->route('user.post-comment.index');
        } else {
            request()->session()->flash('error', 'Comment not found');
            return redirect()->back();
        }
    }
    public function userAddress()
    {
        $id = Auth::user()->id;
        $data = Address::where('user_id', $id)->paginate(10);

        return view('user.address.index')->with('data', $data);
    }


    public function userStoreAddress(Request $request)
    {
        try {
            //            dd($request->all());
            $validasi = Validator::make($request->all(), [
                'firstname' => 'required',
                'email' => 'required',
                'province' => 'required',
                'district' => 'required',
                'lastname' => 'required',
                'phone' => 'required',
                'city' => 'required',
                'address1' => 'required',
                'postcode' => 'required',
                'company' => 'required',
                'priority' => 'required',
            ]);

            if ($validasi->fails()) {
                return back()->withErrors($validasi)->withInput();
            }

            $id = Auth::user()->id;
            Address::create([
                'user_id' => $id,
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
            ]);

            // Redirect ke halaman index kategori dengan pesan sukses
            request()->session()->flash('success', 'Address has been successfully added');
            return redirect()->route('user.address.index');
        } catch (\Exception $e) {
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }

    public function userAddAddress()
    {
        $province =  Http::withHeaders([
            'key' => '067f3c3070f9ba9652054f7f1eb0e182',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get('https://api.rajaongkir.com/starter/province');


        $dataProvince = $province['rajaongkir']['results'];
        return view('user.address.create', compact('dataProvince'));
    }

    public function getCity(Request $request)
    {
        $prov_id = $request->prov_id;

        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get('https://pro.rajaongkir.com/api/city?province=' . $prov_id);

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


    public function changePassword()
    {
        return view('user.layouts.userPasswordChange');
    }
    public function changPasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('user')->with('success', 'Password successfully changed');
    }
}
