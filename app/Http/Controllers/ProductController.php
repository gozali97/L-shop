<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Aws\S3\S3Client;
use Helper;


use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::getAllProduct();

        $photo = [];

        foreach ($products as $p) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $p->photo,
            ]);

            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $preSignedUrl = (string) $presignedRequest->getUri();

            $photo[] = $preSignedUrl;
        }

        return view('backend.product.index')->with('products', $products)->with('photo', $photo);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brand = Brand::get();
        $category = Category::where('is_parent', 1)->get();
        // return $category;
        return view('backend.product.create')->with('categories', $category)->with('brands', $brand);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{

            \DB::beginTransaction();
            $this->validate($request, [
                'title' => 'string|required',
                'summary' => 'string|required',
                'description' => 'string|nullable',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'size' => 'nullable',
                'stock' => "required|numeric",
                'cat_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'child_cat_id' => 'nullable|exists:categories,id',
                'is_featured' => 'sometimes|in:1',
                'status' => 'required|in:active,inactive',
                'condition' => 'required|in:default,new,hot',
                'price' => 'required|numeric',
                'length' => 'required|numeric',
                'width' => 'required|numeric',
                'height' => 'required|numeric',
                'weight' => 'required|numeric',
                'sku' => ['required','string', Rule::unique('products', 'sku')],
            ]);

            $slug = Str::slug($request->title);
            $count = Product::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }

            $data = new Product;
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                $extension = $image->getClientOriginalExtension();
                $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
                $name = 'img_product/' . $fileName;
                // Upload gambar ke Wasabi
                $result = Helper::s3()->putObject([
                    'Bucket' => env('WASABI_BUCKET_NAME'),
                    'Key' => $name,
                    'Body' => file_get_contents($image),
                    'ACL' => 'public-read',
                ]);

                $data->photo = $name;
            }

            $data->title = $request->title;
            $data->slug = $slug;
            $data->sku = $request->sku;
            $data->is_featured = $request->is_featured;
            $data->summary = $request->summary;
            $data->description = $request->description;

            $data->stock = $request->stock;
            $data->cat_id = $request->cat_id;
            $data->cat_id = $request->cat_id;
            $data->brand_id = $request->brand_id;
            $data->child_cat_id = $request->child_cat_id;
            $data->status = $request->status;
            $data->condition = $request->condition;
            $data->price = $request->price;
            $data->length = $request->length;
            $data->width = $request->width;
            $data->height = $request->height;
            $data->weight = $request->weight;
            $size = $request->input('size');

            if ($size) {
                $data->size = implode(',', $size);
            } else {
                $data->size = '';
            }

            if ($data->save()) {
                \DB::commit();
                request()->session()->flash('success', 'Product Successfully added');
                return redirect()->route('product.index');
            }else{
                request()->session()->flash('error', 'Something went wrong! Please try again!!');
            }
        } catch (\Exception $e) {
            \DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }

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
        $brand = Brand::get();
        $product = Product::findOrFail($id);
        $category = Category::where('is_parent', 1)->get();
        $items = Product::where('id', $id)->get();

        $photo = null;

        if ($product->photo) {
            $cmd = Helper::s3()->getCommand('GetObject', [
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $product->photo,
            ]);
            $presignedRequest = Helper::s3()->createPresignedRequest($cmd, '+720 minutes');
            $photo = (string) $presignedRequest->getUri();
        }

        return view('backend.product.edit')
            ->with('product', $product)
            ->with('brands', $brand)
            ->with('categories', $category)
            ->with('items', $items)
            ->with('photo', $photo);
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
        try{

            \DB::beginTransaction();
            $this->validate($request, [
                'title' => 'string|required',
                'summary' => 'string|required',
                'description' => 'string|nullable',
                'size' => 'nullable',
                'stock' => "required|numeric",
                'cat_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'child_cat_id' => 'nullable|exists:categories,id',
                'is_featured' => 'sometimes|in:1',
                'status' => 'required|in:active,inactive',
                'condition' => 'required|in:default,new,hot',
                'price' => 'required|numeric',
                'length' => 'required|numeric',
                'width' => 'required|numeric',
                'height' => 'required|numeric',
                'weight' => 'required|numeric',
                'sku' => ['required','string', Rule::unique('products', 'sku')->ignore($id)],
            ]);

            $slug = Str::slug($request->title);
            $count = Product::where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
            }

            $data = Product::findOrFail($id);
            if ($request->hasFile('image')) {
                $image = $request->file('image');


                $extension = $image->getClientOriginalExtension();
                $fileName = hash('sha256', $image->getClientOriginalName()) . '.' . $extension;
                $name = 'img_product/' . $fileName;
                // Upload gambar ke Wasabi
                $result = Helper::s3()->putObject([
                    'Bucket' => env('WASABI_BUCKET_NAME'),
                    'Key' => $name,
                    'Body' => file_get_contents($image),
                    'ACL' => 'public-read',
                ]);

                $data->photo = $name;
            }

            $data->title = $request->title;
            $data->slug = $slug;
            $data->sku = $request->sku;
            $data->is_featured = $request->is_featured;
            $data->summary = $request->summary;
            $data->description = $request->description;

            $data->stock = $request->stock;
            $data->cat_id = $request->cat_id;
            $data->cat_id = $request->cat_id;
            $data->brand_id = $request->brand_id;
            $data->child_cat_id = $request->child_cat_id;
            $data->status = $request->status;
            $data->condition = $request->condition;
            $data->price = $request->price;
            $data->length = $request->length;
            $data->width = $request->width;
            $data->height = $request->height;
            $data->weight = $request->weight;
            $size = $request->input('size');

            if ($size) {
                $data->size = implode(',', $size);
            } else {
                $data->size = '';
            }

            if ($data->save()) {
                \DB::commit();
                request()->session()->flash('success', 'Product Successfully added');
                return redirect()->route('product.index');
            }else{
                request()->session()->flash('error', 'Something went wrong! Please try again!!');
            }
        } catch (\Exception $e) {
            \DB::rollback();
            request()->session()->flash('error', 'Something went wrong! Please try again!!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->photo) {
            $cmd = Helper::s3()->deleteObject([
                'Bucket' => env('WASABI_BUCKET_NAME'),
                'Key' => $product->photo,
            ]);
        }

        if ($product->delete()) {
            request()->session()->flash('success', 'Product successfully deleted');
        } else {
            request()->session()->flash('error', 'Error while deleting product');
        }
        return redirect()->route('product.index');
    }

}
