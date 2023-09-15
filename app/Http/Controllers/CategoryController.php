<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category=Category::getAllCategory();
        $photo = [];

        foreach ($category as $c) {
            $cmd = \Helper::s3()->getCommand('GetObject', [
                'Bucket' => 'asima',
                'Key' => $c->photo,
            ]);

            $presignedRequest = \Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photo[] = $preSignedUrl;
        }
        return view('backend.category.index')->with('categories',$category)->with('photo', $photo);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_cats=Category::where('is_parent',1)->orderBy('title','ASC')->get();
        return view('backend.category.create')->with('parent_cats',$parent_cats);
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
            'title'=>'string|required',
            'summary'=>'string|nullable',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'=>'required|in:active,inactive',
            'is_parent'=>'sometimes|in:1',
            'parent_id'=>'nullable|exists:categories,id',
        ]);
        $data= $request->all();
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $extension = $image->getClientOriginalExtension();
            $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
            $name = 'img_product_category/' . $fileName;
            // Upload gambar ke Wasabi
            $result = \Helper::s3()->putObject([
                'Bucket' => 'asima',
                'Key' => $name,
                'Body' => file_get_contents($image),
                'ACL' => 'public-read',
            ]);

            $data['photo'] = $name;
        }
        $slug=Str::slug($request->title);
        $count=Category::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $data['is_parent']=$request->input('is_parent',0);

        $status=Category::create($data);
        if($status){
            request()->session()->flash('success','Category successfully added');
        }
        else{
            request()->session()->flash('error','Error occurred, Please try again!');
        }
        return redirect()->route('category.index');


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
        $parent_cats=Category::where('is_parent',1)->get();
        $category=Category::findOrFail($id);

        if ($category->photo) {
            $cmd = \Helper::s3()->getCommand('GetObject', [
                'Bucket' => 'asima',
                'Key' => $category->photo,
            ]);
            $presignedRequest = \Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $photo = (string) $presignedRequest->getUri();
        }
        return view('backend.category.edit')->with('category',$category)->with('parent_cats',$parent_cats)->with('photo', $photo);
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
        // return $request->all();
        $category=Category::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|nullable',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'=>'required|in:active,inactive',
            'is_parent'=>'sometimes|in:1',
            'parent_id'=>'nullable|exists:categories,id',
        ]);
        $data= $request->all();
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $extension = $image->getClientOriginalExtension();
            $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
            $name = 'img_product_category/' . $fileName;
            // Upload gambar ke Wasabi
            $result = \Helper::s3()->putObject([
                'Bucket' => 'asima',
                'Key' => $name,
                'Body' => file_get_contents($image),
                'ACL' => 'public-read',
            ]);

            $data['photo'] = $name;
        }
        $data['is_parent']=$request->input('is_parent',0);
        // return $data;
        $status=$category->fill($data)->save();
        if($status){
            request()->session()->flash('success','Category successfully updated');
        }
        else{
            request()->session()->flash('error','Error occurred, Please try again!');
        }
        return redirect()->route('category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category=Category::findOrFail($id);
        $child_cat_id=Category::where('parent_id',$id)->pluck('id');

        if ($category->photo) {
            $cmd = \Helper::s3()->deleteObject([
                'Bucket' => 'asima',
                'Key' => $category->photo,
            ]);
        }
        $status=$category->delete();

        if($status){
            if(count($child_cat_id)>0){
                Category::shiftChild($child_cat_id);
            }
            request()->session()->flash('success','Category successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting category');
        }
        return redirect()->route('category.index');
    }

    public function getChildByParent(Request $request){
        // return $request->all();
        $category=Category::findOrFail($request->id);
        $child_cat=Category::getChildByParentID($request->id);
        // return $child_cat;
        if(count($child_cat)<=0){
            return response()->json(['status'=>false,'msg'=>'','data'=>null]);
        }
        else{
            return response()->json(['status'=>true,'msg'=>'','data'=>$child_cat]);
        }
    }
}
