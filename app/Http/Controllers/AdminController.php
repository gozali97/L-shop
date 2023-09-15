<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Settings;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Carbon\Carbon;
use Hash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;
use Helper;

class AdminController extends Controller
{

    /**
     * Prepare the data for the index view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $registeredUsers = User::select(DB::raw("COUNT(*) as count"), DB::raw("DAYNAME(created_at) as day_name"), DB::raw("DAY(created_at) as day"))
            ->where('created_at', '>', Carbon::today()->subDay(6))
            ->groupBy('day_name', 'day')
            ->orderBy('day')
            ->get();

        $usersChartData = $this->prepareUsersChartData($registeredUsers);

        $limit = 4;
        $recentOrders = $this->getRecentOrders($limit);
        $topProducts = $this->getTopProducts($limit);
        $recentSignUps = $this->getRecentSignUps($limit);
        $topCustomers = $this->getTopCustomers($limit);
        $currentCoupons = $this->getCurrentCoupons();

        return view('backend.index')
            ->with('users', json_encode($usersChartData))
            ->with('recentOrders', $recentOrders)
            ->with('recentSignUps', $recentSignUps)
            ->with('topCustomers', $topCustomers)
            ->with('currentCoupons', $currentCoupons)
            ->with('topProducts', $topProducts);
    }

    /**
     * Prepare the chart data for registered users.
     *
     * @param  \Illuminate\Support\Collection  $registeredUsers
     * @return array
     */
    private function prepareUsersChartData($registeredUsers)
    {
        $chartData = [['Name', 'Number']];

        foreach ($registeredUsers as $key => $value) {
            $chartData[++$key] = [$value->day_name, $value->count];
        }

        return $chartData;
    }

    /**
     * Get the recent orders.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentOrders($limit)
    {
        return Order::orderBy('id', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the top products.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTopProducts($limit)
    {
        return Product::withCount('carts')
            ->orderBy('carts_count', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the recent sign-ups.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentSignUps($limit)
    {
        return User::where('role', 'user')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the top customers.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTopCustomers($limit)
    {
        return User::withCount('orders')
        ->where('role', 'user')
        ->withSum('orders', 'total_amount')
        ->orderBy('orders_count', 'DESC')
        ->limit($limit)
        ->get();
    }

    /**
     * Get the current coupons.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getCurrentCoupons()
    {
        return Coupon::withCount('orders')
            ->where(function ($query) {
                $query->whereNull('coupons.start_date')
                    ->where('coupons.status', 'active')
                    ->orWhere(function ($query) {
                        $query->whereNotNull('coupons.start_date')
                            ->whereRaw('CURDATE() >= coupons.start_date');
                    });
            })
            ->where(function ($query) {
                $query->whereNull('coupons.end_date')
                    ->where('coupons.status', 'active')
                    ->orWhere(function ($query) {
                        $query->whereNotNull('coupons.end_date')
                            ->whereRaw('CURDATE() <= coupons.end_date');
                    });
            })
            ->orderBy('orders_count', 'DESC')
            ->get();
    }


    /**
     * Prepare the data for the index view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function profile()
    {
        $profile = Auth()->user();
        return view('backend.users.profile')->with('profile', $profile);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function profileUpdate(Request $request, $id): RedirectResponse
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


    /**
     * Show the settings page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function settings()
    {
        $data = Settings::first();
        $photo = null;

        if ($data->photo) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $data->photo,
            ]);
            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $photo = (string) $presignedRequest->getUri();
        }

        $brandLogo = null;

        if ($data->brand_logo) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $data->brand_logo,
            ]);
            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $brandLogo = (string) $presignedRequest->getUri();
        }
        return view('backend.setting')->with('data', $data)->with('photo', $photo)->with('brandLogo', $brandLogo);
    }

    /**
     * Update the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function settingsUpdate(Request $request): RedirectResponse
    {
        $settings = Settings::find(1);
        $this->validate($request, [
            'brand_name' => 'required|string',
            'short_des' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'socmed_wa' => ['required', 'numeric'],
            'socmed_instagram' => ['string', 'nullable'],
            'socmed_facebook' => ['string', 'nullable'],
            'theme' => 'required',
        ]);
        $data = $request->all();
        if ($request->hasFile('brand_logo')) {
            $image = $request->file('brand_logo');

            // Konfigurasi koneksi ke Wasabi

            $extension = $image->getClientOriginalExtension();
            $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
            $name = 'assets/' . $fileName;

            $result = Helper::s3()->putObject([
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $name,
                'Body' => file_get_contents($image),
                'ACL' => 'public-read',
            ]);

            $data['brand_logo'] = $name;
        }else{
            $data['brand_logo'] = $settings->brand_logo;
        }
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');

            // Konfigurasi koneksi ke Wasabi

            $extension = $image->getClientOriginalExtension();
            $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
            $name = 'assets/' . $fileName;

            $result = Helper::s3()->putObject([
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $name,
                'Body' => file_get_contents($image),
                'ACL' => 'public-read',
            ]);

            $data['photo'] = $name;
        }else{
            $data['photo'] = $settings->photo;
        }

        $status = $settings->fill($data)->save();
        if ($status) {
            request()->session()->flash('success','Setting successfully updated');
        } else {
            request()->session()->flash('error','Please try again');
        }
        return redirect()->route('admin');
    }

    /**
     * Show the change password page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function changePassword()
    {
        return view('backend.layouts.changePassword');
    }

    /**
     * Store the changed password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changPasswordStore(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('admin')->with('success', 'Password successfully changed');
    }

    /**
     * Show the user pie chart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function userPieChart(Request $request)
    {
        // dd($request->all());
        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
            ->where('created_at', '>', Carbon::today()->subDay(6))
            ->groupBy('day_name', 'day')
            ->orderBy('day')
            ->get();
        $array[] = ['Name', 'Number'];
        foreach ($data as $key => $value) {
            $array[++$key] = [$value->day_name, $value->count];
        }
        //  return $data;
        return view('backend.index')->with('course', json_encode($array));
    }


    /**
     * Generate or regenerate the storage link.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function storageLink(): RedirectResponse
    // {
    //     // check if the storage folder already linked;
    //     if (File::exists(public_path('storage'))) {
    //         // removed the existing symbolic link
    //         File::delete(public_path('storage'));

    //         //Regenerate the storage link folder
    //         try {
    //             Artisan::call('storage:link');
    //             request()->session()->flash('success', 'Successfully storage linked.');
    //             return redirect()->back();
    //         } catch (\Exception $exception) {
    //             request()->session()->flash('error', $exception->getMessage());
    //             return redirect()->back();
    //         }
    //     } else {
    //         try {
    //             Artisan::call('storage:link');
    //             request()->session()->flash('success', 'Successfully storage linked.');
    //             return redirect()->back();
    //         } catch (\Exception $exception) {
    //             request()->session()->flash('error', $exception->getMessage());
    //             return redirect()->back();
    //         }
    //     }
    // }
}
