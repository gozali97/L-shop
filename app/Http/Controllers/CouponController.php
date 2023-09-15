<?php

namespace App\Http\Controllers;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\Cart;
class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupon=Coupon::orderBy('id','DESC')->paginate('10');
        return view('backend.coupon.index')->with('coupons',$coupon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $this->validate($request,[
            'code'=>'string|required',
            'type'=>'required|in:fixed,percent',
            'value'=>'required|numeric',
            'status'=>'required|in:active,inactive',
            'coupons_name' => 'string|required',
            'start_date' => 'date|before:end_date|nullable',
            'end_date' => 'date|after:start_date|nullable',
        ]);
        $data=$request->all();

        // If today is equal or greater than start date and the status is not active, then set status as active
        if (!is_null($data['start_date'])) {
            if (date('Y-m-d') >= $data['start_date'] && $data['status'] !== 'active') {
                $data['status'] = 'active';
            }

            // If today is lesser than today and the status is active, then set status as inactive
            if (date('Y-m-d') < $data['start_date'] && $data['status'] === 'active') {
                $data['status'] = 'inactive';
            }
        }

        // If end date is greater than today and the status is still active, then set status as expired
        if (!is_null($data['end_date'])) {
            if (date('Y-m-d') > $data['end_date'] && $data['status'] === 'active') {
                $data['status'] = 'expired';
            }
        }

        $status=Coupon::create($data);
        if($status){
            request()->session()->flash('success','Coupon Successfully added');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('coupon.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $coupon=Coupon::find($id);
        if($coupon){
            return view('backend.coupon.edit')->with('coupon',$coupon);
        }
        else{
            return view('backend.coupon.index')->with('error','Coupon not found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $coupon=Coupon::find($id);
        $this->validate($request,[
            'code'=>'string|required',
            'type'=>'required|in:fixed,percent',
            'value'=>'required|numeric',
            'status'=>'required|in:active,inactive',
            'coupons_name' => 'string|required',
            'start_date' => 'date|before:end_date|nullable',
            'end_date' => 'date|after:start_date|nullable',
        ]);
        $data=$request->all();

        // If today is equal or greater than start date and the status is not active, then set status as active
        if (!is_null($data['start_date'])) {
            if (date('Y-m-d') >= $data['start_date'] && $data['status'] !== 'active') {
                $data['status'] = 'active';
            }

            // If today is lesser than today and the status is active, then set status as inactive
            if (date('Y-m-d') < $data['start_date'] && $data['status'] === 'active') {
                $data['status'] = 'inactive';
            }
        }

        // If end date is greater than today and the status is still active, then set status as expired
        if (!is_null($data['end_date'])) {
            if (date('Y-m-d') > $data['end_date'] && $data['status'] === 'active') {
                $data['status'] = 'expired';
            }
        }

        $status=$coupon->fill($data)->save();
        if($status){
            request()->session()->flash('success','Coupon Successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('coupon.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon=Coupon::find($id);
        if($coupon){
            $status=$coupon->delete();
            if($status){
                request()->session()->flash('success','Coupon successfully deleted');
            }
            else{
                request()->session()->flash('error','Error, Please try again');
            }
            return redirect()->route('coupon.index');
        }
        else{
            request()->session()->flash('error','Coupon not found');
            return redirect()->back();
        }
    }

    public function couponStore(Request $request){
        // return $request->all();
        $coupon=Coupon::where('code',$request->code)->first();
        // dd($coupon);
        if(!$coupon){
            request()->session()->flash('error','Invalid coupon code, Please try again');
            return back();
        }
        if($coupon){
            $total_price=Cart::where('user_id',auth()->user()->id)->where('order_id',null)->sum('price');
            // dd($total_price);
            session()->put('coupon',[
                'id'=>$coupon->id,
                'code'=>$coupon->code,
                'value'=>$coupon->discount($total_price)
            ]);
            request()->session()->flash('success','Coupon successfully applied');
            return redirect()->back();
        }
    }
}
