<?php

namespace App\Http\Controllers;

use App\Models\ShippingFee;
use Illuminate\Http\Request;

class ShippingFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shipping= ShippingFee::find(1);
        $data = json_decode($shipping->courir);

        return view('backend.shipping.index', compact('data', 'shipping'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $shipping = ShippingFee::find($id);
        $data = json_decode($shipping->courir, true);

        $shipping->amount = $request->amount;
        $shipping->amount_type = $request->type;

        foreach ($data as $key => $value) {
            if (isset($request->status[$key])) {
                $data[$key] = $request->status[$key] === 'on';
            } else {
                $data[$key] = false;
            }
        }

        $shipping->courir = json_encode($data);

        if($shipping->save()){
            request()->session()->flash('success','Shipping Fee successfully updated');
        }
        else{
            request()->session()->flash('error','Error, Please try again');
        }
        return redirect()->route('shipping.index');
    }

}
