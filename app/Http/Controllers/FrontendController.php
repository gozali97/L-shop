<?php

namespace App\Http\Controllers;
use App\Mail\ResetPasswordEmail;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use App\Models\Product;
use App\Models\Settings;
use App\Models\User;
use Auth;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Newsletter;
use Session;
use Helper;

class FrontendController extends Controller
{

    public function index(Request $request){
        return redirect()->route($request->user()->role);
    }
    public function home(){
        $featured=Product::where('status','active')->where('is_featured',1)->orderBy('price','DESC')->limit(2)->get();
        $photoFeatured = [];

        foreach ($featured as $f) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $f->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoFeatured[] = $preSignedUrl;
        }

        $posts=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();

        $banners=Banner::where('status','active')->limit(3)->orderBy('id','DESC')->get();
        $photoBanner = [];

        foreach ($banners as $b) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $b->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoBanner[] = $preSignedUrl;
        }
        // return $banner;
        $products=Product::where('status','active')->orderBy('id','DESC')->limit(8)->get();
        $photoProduct = [];

        foreach ($products as $p) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $p->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoProduct[] = $preSignedUrl;
        }

        $productLatest=Product::where('status','active')->orderBy('id','DESC')->limit(6)->get();
        $photoNewProduct = [];

        foreach ($productLatest as $pn) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $pn->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoNewProduct[] = $preSignedUrl;
        }

//        $category=Category::where('status','active')->where('is_parent',1)->orderBy('title','ASC')->get();
        $category=Category::where('status','active')->limit(3)->get();
        $photoCategory = [];

        foreach ($category as $c) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $c->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoCategory[] = $preSignedUrl;
        }
        // return $category;
        return view('frontend.index')
                ->with('featured',$featured)
                ->with('posts',$posts)
                ->with('banners',$banners)
                ->with('product_lists',$products)
                ->with('productLatest',$productLatest)
                ->with('photoFeatured',$photoFeatured)
                ->with('photoBanner',$photoBanner)
                ->with('photoProduct',$photoProduct)
                ->with('photoNewProduct',$photoNewProduct)
                ->with('photoCategory',$photoCategory)
                ->with('category_lists',$category);
    }

    public function aboutUs(){
        return view('frontend.pages.about-us');
    }

    public function contact(){
        return view('frontend.pages.contact');
    }

    public function productDetail($slug){
        $product_detail= Product::getProductBySlug($slug);
        $photo = null;

        if ($product_detail->photo) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $product_detail->photo,
            ]);
            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $photo = (string) $presignedRequest->getUri();
        }
        return view('frontend.pages.product_detail')->with('product_detail',$product_detail)->with('photo', $photo);
    }

    public function productGrids(){
        $products=Product::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // dd($cat_ids);
            $products->whereIn('cat_id',$cat_ids);
            // return $products;
        }
        if(!empty($_GET['brand'])){
            $slugs=explode(',',$_GET['brand']);
            $brand_ids=Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            return $brand_ids;
            $products->whereIn('brand_id',$brand_ids);
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $products=$products->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $products=$products->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);
            // return $price;
            // if(isset($price[0]) && is_numeric($price[0])) $price[0]=floor(Helper::base_amount($price[0]));
            // if(isset($price[1]) && is_numeric($price[1])) $price[1]=ceil(Helper::base_amount($price[1]));

            $products->whereBetween('price',$price);
        }

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $photoRecenProduct = [];

        foreach ($recent_products as $rp) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $rp->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoRecenProduct[] = $preSignedUrl;
        }

        if(!empty($_GET['show'])){
            $products=$products->where('status','active')->paginate($_GET['show']);
        }
        else{
            $products=$products->where('status','active')->paginate(9);
        }

        $photoProduct = [];

        foreach ($products as $p) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $p->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoProduct[] = $preSignedUrl;
        }

        return view('frontend.pages.product-grids')
            ->with('products',$products)
            ->with('photoProduct',$photoProduct)
            ->with('photoRecenProduct',$photoRecenProduct)
            ->with('recent_products',$recent_products);
    }
    public function productLists(){
        $products=Product::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // dd($cat_ids);
            $products->whereIn('cat_id',$cat_ids)->paginate;
            // return $products;
        }
        if(!empty($_GET['brand'])){
            $slugs=explode(',',$_GET['brand']);
            $brand_ids=Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            return $brand_ids;
            $products->whereIn('brand_id',$brand_ids);
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $products=$products->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $products=$products->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);
            // return $price;
            // if(isset($price[0]) && is_numeric($price[0])) $price[0]=floor(Helper::base_amount($price[0]));
            // if(isset($price[1]) && is_numeric($price[1])) $price[1]=ceil(Helper::base_amount($price[1]));

            $products->whereBetween('price',$price);
        }

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $photoRecenProduct = [];

        foreach ($recent_products as $rp) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $rp->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoRecenProduct[] = $preSignedUrl;
        }

        if(!empty($_GET['show'])){
            $products=$products->where('status','active')->paginate($_GET['show']);
        }
        else{
            $products=$products->where('status','active')->paginate(6);
        }
        // Sort by name , price, category

        $photoProduct = [];

        foreach ($products as $p) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $p->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoProduct[] = $preSignedUrl;
        }
        return view('frontend.pages.product-lists')
            ->with('products',$products)
            ->with('photoProduct',$photoProduct)
            ->with('photoRecenProduct',$photoRecenProduct)
            ->with('recent_products',$recent_products);
    }
    public function productFilter(Request $request){
            $data= $request->all();
            // return $data;
            $showURL="";
            if(!empty($data['show'])){
                $showURL .='&show='.$data['show'];
            }

            $sortByURL='';
            if(!empty($data['sortBy'])){
                $sortByURL .='&sortBy='.$data['sortBy'];
            }

            $catURL="";
            if(!empty($data['category'])){
                foreach($data['category'] as $category){
                    if(empty($catURL)){
                        $catURL .='&category='.$category;
                    }
                    else{
                        $catURL .=','.$category;
                    }
                }
            }

            $brandURL="";
            if(!empty($data['brand'])){
                foreach($data['brand'] as $brand){
                    if(empty($brandURL)){
                        $brandURL .='&brand='.$brand;
                    }
                    else{
                        $brandURL .=','.$brand;
                    }
                }
            }
            // return $brandURL;

            $priceRangeURL="";
            if(!empty($data['price_range'])){
                $priceRangeURL .='&price='.$data['price_range'];
            }
            if(request()->is('e-shop.loc/product-grids')){
                return redirect()->route('product-grids',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
            else{
                return redirect()->route('product-lists',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
    }
    public function productSearch(Request $request){
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $photoRecenProduct = [];


        foreach ($recent_products as $rp) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $rp->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoRecenProduct[] = $preSignedUrl;
        }

        $products=Product::orwhere('title','like','%'.$request->search.'%')
                    ->orwhere('slug','like','%'.$request->search.'%')
                    ->orwhere('description','like','%'.$request->search.'%')
                    ->orwhere('summary','like','%'.$request->search.'%')
                    ->orwhere('price','like','%'.$request->search.'%')
                    ->orderBy('id','DESC')
                    ->paginate('9');

        $photoProduct = [];

        foreach ($products as $p) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $p->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoProduct[] = $preSignedUrl;
        }
        return view('frontend.pages.product-grids')
            ->with('products',$products)
            ->with('photoProduct',$photoProduct)
            ->with('photoRecenProduct',$photoRecenProduct)
            ->with('recent_products',$recent_products);
    }

    public function productBrand(Request $request){
        $products=Brand::getProductByBrand($request->slug);
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $photoRecenProduct = [];

        foreach ($recent_products as $rp) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $rp->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoRecenProduct[] = $preSignedUrl;
        }

        $photoProduct = [];

        foreach ($products as $p) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $p->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoProduct[] = $preSignedUrl;
        }

        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')
                ->with('products',$products->products)
                ->with('photoProduct',$photoProduct)
                ->with('photoRecenProduct',$photoRecenProduct)
                ->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')
                ->with('products',$products->products)
                ->with('photoProduct',$photoProduct)
                ->with('photoRecenProduct',$photoRecenProduct)
                ->with('recent_products',$recent_products);
        }

    }
    public function productCat(Request $request){
        $products=Category::getProductByCat($request->slug);

        $photoProduct = null;

        if ($products->photo) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $products->photo,
            ]);
            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $photoProduct = (string) $presignedRequest->getUri();
        }

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();

        $photoRecenProduct = [];

        foreach ($recent_products as $rp) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $rp->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoRecenProduct[] = $preSignedUrl;
        }


        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')
                ->with('products',$products->products)
                ->with('photoProduct',$photoProduct)
                ->with('photoRecenProduct',$photoRecenProduct)
                ->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')
                ->with('products',$products->products)
                ->with('photoProduct',$photoProduct)
                ->with('photoRecenProduct',$photoRecenProduct)
                ->with('recent_products',$recent_products);
        }

    }
    public function productSubCat(Request $request){
        $products=Category::getProductBySubCat($request->sub_slug);
        $photoProduct = null;

        if ($products->photo) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $products->photo,
            ]);
            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $photoProduct = (string) $presignedRequest->getUri();
        }

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $photoRecenProduct = [];

        foreach ($recent_products as $rp) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $rp->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photoRecenProduct[] = $preSignedUrl;
        }

        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')
                ->with('products',$products->sub_products)
                ->with('photoProduct',$photoProduct)
                ->with('photoRecenProduct',$photoRecenProduct)
                ->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')
                ->with('products',$products->sub_products)
                ->with('photoProduct',$photoProduct)
                ->with('photoRecenProduct',$photoRecenProduct)
                ->with('recent_products',$recent_products);
        }

    }

    public function blog(){
        $post=Post::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=PostCategory::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            return $cat_ids;
            $post->whereIn('post_cat_id',$cat_ids);
            // return $post;
        }
        if(!empty($_GET['tag'])){
            $slug=explode(',',$_GET['tag']);
            // dd($slug);
            $tag_ids=PostTag::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // return $tag_ids;
            $post->where('post_tag_id',$tag_ids);
            // return $post;
        }

        if(!empty($_GET['show'])){
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate($_GET['show']);
        }
        else{
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate(9);
        }
        // $post=Post::where('status','active')->paginate(8);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogDetail($slug){
        $post=Post::getPostBySlug($slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // return $post;
        return view('frontend.pages.blog-detail')->with('post',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogSearch(Request $request){
        // return $request->all();
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $posts=Post::orwhere('title','like','%'.$request->search.'%')
            ->orwhere('quote','like','%'.$request->search.'%')
            ->orwhere('summary','like','%'.$request->search.'%')
            ->orwhere('description','like','%'.$request->search.'%')
            ->orwhere('slug','like','%'.$request->search.'%')
            ->orderBy('id','DESC')
            ->paginate(8);
        return view('frontend.pages.blog')->with('posts',$posts)->with('recent_posts',$rcnt_post);
    }

    public function blogFilter(Request $request){
        $data=$request->all();
        // return $data;
        $catURL="";
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .='&category='.$category;
                }
                else{
                    $catURL .=','.$category;
                }
            }
        }

        $tagURL="";
        if(!empty($data['tag'])){
            foreach($data['tag'] as $tag){
                if(empty($tagURL)){
                    $tagURL .='&tag='.$tag;
                }
                else{
                    $tagURL .=','.$tag;
                }
            }
        }
        // return $tagURL;
            // return $catURL;
        return redirect()->route('blog',$catURL.$tagURL);
    }

    public function blogByCategory(Request $request){
        $post=PostCategory::getBlogByCategory($request->slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post->post)->with('recent_posts',$rcnt_post);
    }

    public function blogByTag(Request $request){
        // dd($request->slug);
        $post=Post::getBlogByTag($request->slug);
        // return $post;
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    // Login
    public function login(){
        return view('frontend.pages.login');
    }
    public function loginSubmit(Request $request)
    {
        $data = $request->all();

        $user = User::where('email', $data['email'])
            ->where('status', 'active')
            ->first();

        if ($user && Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            if ($user->hasVerifiedEmail()) { // Tambahkan pemeriksaan email terverifikasi di sini
                Session::put('user', $data['email']);
                request()->session()->flash('success', 'Successfully login');
                return redirect()->route('home');
            } else {
                Auth::logout(); // Keluarkan pengguna dari sesi
                return redirect()->route('verification.notice'); // Arahkan ke halaman verifikasi
            }
        } else {
            request()->session()->flash('error', 'Invalid email and password or email is not verified. Please try again.');
            return redirect()->back();
        }
    }


    public function logout(){
        Session::forget('user');
        Auth::logout();
        request()->session()->flash('success','Logout successfully');
        return redirect()->route('home');
    }

    public function register(){
        return view('frontend.pages.register');
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
    public function registerSubmit(Request $request){
        $this->validate($request, [
            'name' => 'string|required|min:2',
            'email' => 'string|required|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $data = $request->all();
        $user = $this->create($data);

        $token = sha1($user->getEmailForVerification());
        $user->email_verified_token = $token;
        $user->save();

        \Illuminate\Support\Facades\Session::put('user', $data['email']);
        $setting = Settings::find(1);

        $emaildata = [
            'user' => $user,
            'settings'      => $setting,
            'brandLogo'      => $this->getBrandLogoUrl(),
            'email'         => $user->email,
            'token'         => $token,
        ];

        if ($user) {
            Mail::send('email.email-welcome',  $emaildata, function ($message) use ($emaildata) {
                $message->to($emaildata['email'])->subject('Konfirmasi Akun Pelanggan');
            });

            request()->session()->flash('success', 'Successfully registered');
            return redirect()->route('home');
        } else {
            request()->session()->flash('error', 'Please try again!');
            return back();
        }
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User with this email address does not exist.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/home')->with('success', 'Email already verified.');
        }

        $token = sha1($user->getEmailForVerification());
        $user->email_verified_token = $token;
        $user->save();

        $setting = Settings::find(1);

        $emaildata = [
            'user' => $user,
            'settings' => $setting,
            'brandLogo' => $this->getBrandLogoUrl(),
            'email' => $user->email,
            'token' => $token,
        ];

        Mail::send('email.email-welcome',  $emaildata, function ($message) use ($emaildata) {
            $message->to($emaildata['email'])->subject('Konfirmasi Akun Pelanggan');
        });

        return back()->with('resent', true);
    }

    public function verify($id, $hash){
        $user = User::find($id);
        if ($user && hash_equals($hash, sha1($user->getEmailForVerification()))) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
                request()->session()->flash('success', 'Email successfully verified. Please log in.');
            } else {
                request()->session()->flash('info', 'Email already verified.');
            }
        } else {
            request()->session()->flash('error', 'Invalid verification link.');
        }

        return redirect()->route('login');
    }

    public function create(array $data){
        return User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>\Hash::make($data['password']),
            'status'=>'active'
            ]);
    }
    // Reset password
    public function showResetForm(){
        return view('auth.passwords.request');
    }

    public function sendMailReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                $emaildata = [
                    'email' => $user->email,
                    'name' => $user->name,
                    'token' => $token,
                ];

                Mail::send('email.email-resetpass', $emaildata, function ($message) use ($emaildata) {
                    $message->to($emaildata['email'])->subject('Reset Password Notification');
                });
            }
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(['status' => __('Reset link sent! Please check your email.')]);
        } else {
            return back()->withErrors(['email' => __($status)]);
        }
    }

    public function subscribe(Request $request){
        if(! Newsletter::isSubscribed($request->email)){
                Newsletter::subscribePending($request->email);
                if(Newsletter::lastActionSucceeded()){
                    request()->session()->flash('success','Subscribed! Please check your email');
                    return redirect()->route('home');
                }
                else{
                    Newsletter::getLastError();
                    return back()->with('error','Something went wrong! please try again');
                }
            }
            else{
                request()->session()->flash('error','Already Subscribed');
                return back();
            }
    }

}
