<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Str;
use Aws\S3\S3Client;
class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banner=Banner::orderBy('id','DESC')->paginate(10);
        $photo = [];

        foreach ($banner as $b) {
            $cmd = \Helper::s3()->getCommand('GetObject', [
                'Bucket' => 'asima',
                'Key' => $b->photo,
            ]);

            $presignedRequest = \Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photo[] = $preSignedUrl;
        }
        return view('backend.banner.index')->with('banners',$banner)->with('photo', $photo);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.banner.create');
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
            'title'=>'string|required|max:50',
            'description'=>'string|nullable',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'=>'required|in:active,inactive',
        ]);

        $data=$request->all();
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $extension = $image->getClientOriginalExtension();
            $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
            $name = 'banner/' . $fileName;
            // Upload gambar ke Wasabi
            $result = \Helper::s3()->putObject([
                'Bucket' => 'asima',
                'Key' => $name,
                'Body' => file_get_contents($image),
                'ACL' => 'public-read',
            ]);

        }

        $data['photo'] = $name;
        $slug=Str::slug($request->title);
        $count=Banner::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        // return $slug;
        $status=Banner::create($data);
        if($status){
            request()->session()->flash('success','Banner successfully added');
        }
        else{
            request()->session()->flash('error','Error occurred while adding banner');
        }
        return redirect()->route('banner.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $banner=Banner::findOrFail($id);
        $photo = null;

        if ($banner->photo) {
            $cmd = \Helper::s3()->getCommand('GetObject', [
                'Bucket' => 'asima',
                'Key' => $banner->photo,
            ]);
            $presignedRequest = \Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $photo = (string) $presignedRequest->getUri();
        }
        return view('backend.banner.edit')->with('banner',$banner)->with('photo', $photo);
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
        $banner=Banner::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required|max:50',
            'description'=>'string|nullable',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'=>'required|in:active,inactive',
        ]);
        $data=$request->all();
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $extension = $image->getClientOriginalExtension();
            $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
            $name = 'banner/' . $fileName;
            // Upload gambar ke Wasabi
            $result = \Helper::s3()->putObject([
                'Bucket' => 'asima',
                'Key' => $name,
                'Body' => file_get_contents($image),
                'ACL' => 'public-read',
            ]);

        }

        $data['photo'] = $name;
        $slug=Str::slug($request->title);
        $count=Banner::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $status=$banner->fill($data)->save();
        if($status){
            request()->session()->flash('success','Banner successfully updated');
        }
        else{
            request()->session()->flash('error','Error occurred while updating banner');
        }
        return redirect()->route('banner.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner=Banner::findOrFail($id);

        if ($banner->photo) {
            $cmd = \Helper::s3()->deleteObject([
                'Bucket' => 'asima',
                'Key' => $banner->photo,
            ]);
        }

        $status=$banner->delete();

        if($status){
            request()->session()->flash('success','Banner successfully deleted');
        }
        else{
            request()->session()->flash('error','Error occurred while deleting banner');
        }
        return redirect()->route('banner.index');
    }
}
