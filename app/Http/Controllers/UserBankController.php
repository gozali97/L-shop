<?php

namespace App\Http\Controllers;

use App\Models\UserBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $id = Auth::user()->id;
        $data = UserBank::where('user_id', $id)->first();
        $path = public_path('json/bank.json');
        $bank_list = json_decode(file_get_contents($path), true);
        return view('user.bank.index')->with('data', $data)->with('bank', $bank_list);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $path = public_path('json/bank.json');
        $bank_list = json_decode(file_get_contents($path), true);

        return view('user.bank.create')->with('bank', $bank_list);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = Auth::user()->id;

            $bank = new UserBank;
            $bank->user_id = $id;
            $bank->bank_name = $request->bank_name;
            $bank->branch_name = $request->branch_name;
            $bank->account_name = $request->account_name;
            $bank->account_number = $request->account_number;
            $bank->status = 'active';

            if($bank->save()){
                request()->session()->flash('success', 'Successfully add your bank');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
        return redirect()->route('user.bank.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user_id = Auth::user()->id;

            $bank = UserBank::find($id);
            $bank->user_id = $user_id;
            $bank->bank_name = $request->bank_name;
            $bank->branch_name = $request->branch_name;
            $bank->account_name = $request->account_name;
            $bank->account_number = $request->account_number;
            $bank->balance = $request->balance;
            $bank->status = 'active';

            if($bank->save()){
                request()->session()->flash('success', 'Successfully update your bank');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
        return redirect()->route('user.bank.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
