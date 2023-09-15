<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankRequest;
use App\Models\Bank;
use Helper;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function index(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $bank = Bank::orderBy('id')->paginate(10);
        $path = public_path('json/bank.json');
        $bank_list = json_decode(file_get_contents($path), true);
        // $photo = [];

        // foreach ($bank as $b) {
        //     $cmd = Helper::s3()->getCommand('GetObject', [
        //         'Bucket' => env('WASABI_BUCKET_NAME'),
        //         'Key' => $b->bank_logo,
        //     ]);

        //     $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
        //     $preSignedUrl = (string) $presignedRequest->getUri();

        //     $photo[] = $preSignedUrl;
        // }

        return view('backend.bank.index', [
            'banks' => $bank,
            'bank_list' => $bank_list,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function create(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $path = public_path('json/bank.json');
        $banks = json_decode(file_get_contents($path), true);
        return view('backend.bank.create', [
            'banks' => $banks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreBankRequest $request
     * @return RedirectResponse
     */
    public function store(StoreBankRequest $request): RedirectResponse
    {
        try {

            $validator = Validator::make($request->all(), [
                'bank_name' => 'string|required|max:50',
                'branch_name' => 'string|required|max:100',
                'account_name' => 'string|required|max:100',
                'account_number' => 'required|numeric',
                // 'bank_logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                #TODO #66
                Log::error('Create bank: An error occurred');
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->all();
            // if ($request->hasFile('bank_logo')) {
            //     $image = $request->file('bank_logo');

            //     $extension = $image->getClientOriginalExtension();
            //     $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
            //     $name = 'bank_logo/' . $fileName;
            //     // Upload gambar ke Wasabi
            //     $result = Helper::s3()->putObject([
            //         'Bucket' => 'asima',
            //         'Key' => $name,
            //         'Body' => file_get_contents($image),
            //         'ACL' => 'public-read',
            //     ]);
            // }
            // $data['bank_logo'] = $name;
            $bank = Bank::create($data);
            if ($bank) {
                # TODO #66
                Log::info('Create bank: Bank successfully added');
                request()->session()->flash('success', 'Bank successfully added');
            }
            return redirect()->route('bank.index');
        } catch (\Exception $e) {
            # TODO #66
            Log::warning('Create bank: An error occurred ');
            request()->session()->flash('error', 'Please try again!');
            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param Bank $bank
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function edit(Bank $bank): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        // $photo = null;

        // if ($bank->bank_logo) {
        //     $cmd = Helper::s3()->getCommand('GetObject', [
        //         'Bucket' => 'asima',
        //         'Key' => $bank->bank_logo,
        //     ]);
        //     $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
        //     $photo = (string) $presignedRequest->getUri();
        // }

        $path = public_path('json/bank.json');
        $bank_list = json_decode(file_get_contents($path), true);

//      #TODO #66
        $message = 'Edit bank';
        Log::info($message);

        // \DB::connection('logging_db')->table('logs')->insert([
        //     'create_by' => \Auth::user()->name,
        //     'message' => $message
        // ]);

        return view('backend.bank.edit', [
            'bank' => $bank,
            'bank_list' => $bank_list
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateBankRequest $request
     * @param Bank $bank
     * @return RedirectResponse
     */
    public function update(Request $request, Bank $bank)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bank_name' => 'string|required|max:50',
                'branch_name' => 'string|required|max:100',
                'account_name' => 'string|required|max:100',
                'account_number' => 'required|numeric',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                Log::error('Update bank: An error occurred');
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->all();
            // if ($request->hasFile('bank_logo')) {
            //     $image = $request->file('bank_logo');

            //     $extension = $image->getClientOriginalExtension();
            //     $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
            //     $name = 'bank_logo/' . $fileName;
            //     // Upload gambar ke Wasabi
            //     $result = Helper::s3()->putObject([
            //         'Bucket' => 'asima',
            //         'Key' => $name,
            //         'Body' => file_get_contents($image),
            //         'ACL' => 'public-read',
            //     ]);

            //     $data['bank_logo'] = $name;
            // } else {
            //     // Jika tidak ada file bank_logo yang diunggah, gunakan nilai default dari model $bank
            //     $data['bank_logo'] = $bank->bank_logo;
            // }

            $status = $bank->fill($data)->save();
            if ($status) {
//                #TODO #66
                $message = 'Update bank: '.$request->bank_name.' '.$request->branch_name.' '.$request->account_name.' '.$request->account_number.'';
                Log::info($message);

                // \DB::connection('logging_db')->table('logs')->insert([
                //     'create_by' => \Auth::user()->name,
                //     'message' => $message,
                // ]);

                request()->session()->flash('success', 'Bank successfully updated');
            }

            return redirect()->route('bank.index');
        } catch (\Exception $e) {
            // Cetak pesan kesalahan jika diperlukan
            echo $e->getMessage();
//            #TODO #66
            Log::error('Update bank: An error occurred');
            request()->session()->flash('error', 'Please try again!');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Bank $bank
     * @return RedirectResponse
     */
    public function destroy(Bank $bank): RedirectResponse
    {
        try {
            $bank->delete();
            #TODO #66
            Log::info('Delete bank: Bank successfully deleted');
            request()->session()->flash('success', 'Bank successfully deleted');
        } catch (\Exception $e) {
            #TODO #66
            Log::error('Delete bank: Error while deleting bank');
            request()->session()->flash('error', 'Error while deleting bank');
        } finally {
            return back();
        }
    }
}
