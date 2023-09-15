@extends('backend.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Edit Product</title>
@section('main-content')
    <div class="p-4">
<div class="card">
    <h5 class="card-header">Edit Product</h5>
    <div class="card-body">
      <form method="post" action="{{route('product.update',$product->id)}}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{$product->title}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

          <div class="form-group">
              <label for="inputSku" class="col-form-label">SKU <span class="text-danger">*</span></label>
              <input id="inputSku" type="text" name="sku" placeholder="Enter SKU"  value="{{old('sku', $product->sku)}}" class="form-control">
              @error('sku')
              <span class="text-danger">{{$errors->first('sku')}}</span>
              @enderror
          </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
          <textarea class="form-control" id="summary" name="summary">{{$product->summary}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{$product->description}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="is_featured">Is Featured</label><br>
          <input type="checkbox" name='is_featured' id='is_featured' value='{{$product->is_featured}}' {{(($product->is_featured) ? 'checked' : '')}}> Yes
        </div>
              {{-- {{$categories}} --}}

        <div class="form-group">
          <label for="cat_id">Category <span class="text-danger">*</span></label>
          <select name="cat_id" id="cat_id" class="form-control">
              <option value="">--Select any category--</option>
              @foreach($categories as $key=>$cat_data)
                  <option value='{{$cat_data->id}}' {{(($product->cat_id==$cat_data->id)? 'selected' : '')}}>{{$cat_data->title}}</option>
              @endforeach
          </select>
        </div>
        @php
          $sub_cat_info=DB::table('categories')->select('title')->where('id',$product->child_cat_id)->get();
        // dd($sub_cat_info);

        @endphp
        {{-- {{$product->child_cat_id}} --}}
        <div class="form-group {{(($product->child_cat_id)? '' : 'd-none')}}" id="child_cat_div">
          <label for="child_cat_id">Sub Category</label>
          <select name="child_cat_id" id="child_cat_id" class="form-control">
              <option value="">--Select any sub category--</option>

          </select>
        </div>

        <div class="form-group">
          <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
          <input id="price" type="number" name="price" placeholder="Enter price"  value="{{$product->price}}" class="form-control">
          @error('price')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="discount" class="col-form-label">Discount(%)</label>
          <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount"  value="{{$product->discount}}" class="form-control">
          @error('discount')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="size">Size</label>
          <select name="size[]" class="form-control selectpicker"  multiple data-live-search="true">
              <option value="">--Select any size--</option>
              @foreach($items as $item)
                @php
                $data=explode(',',$item->size);
                // dd($data);
                @endphp
              <option value="S"  @if( in_array( "S",$data ) ) selected @endif>Small</option>
              <option value="M"  @if( in_array( "M",$data ) ) selected @endif>Medium</option>
              <option value="L"  @if( in_array( "L",$data ) ) selected @endif>Large</option>
              <option value="XL"  @if( in_array( "XL",$data ) ) selected @endif>Extra Large</option>
              @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="brand_id">Brand</label>
          <select name="brand_id" class="form-control">
              <option value="">--Select Brand--</option>
             @foreach($brands as $brand)
              <option value="{{$brand->id}}" {{(($product->brand_id==$brand->id)? 'selected':'')}}>{{$brand->title}}</option>
             @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="condition">Condition</label>
          <select name="condition" class="form-control">
              <option value="">--Select Condition--</option>
              <option value="default" {{(($product->condition=='default')? 'selected':'')}}>Default</option>
              <option value="new" {{(($product->condition=='new')? 'selected':'')}}>New</option>
              <option value="hot" {{(($product->condition=='hot')? 'selected':'')}}>Hot</option>
          </select>
        </div>

        <div class="form-group">
          <label for="stock">Quantity <span class="text-danger">*</span></label>
          <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"  value="{{$product->stock}}" class="form-control">
          @error('stock')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
          <div class="form-group row">
              <div class="col-md-3 form-group">
                  <label class="form-label">Length<span class="text-danger">*</span></label>
                  <div class="input-group mb-3">
                      <input type="number" name="length" value="{{old('length',$product->length)}}" class="form-control" placeholder="Length">
                      <span class="input-group-text" id="basic-addon2">mm</span>
                  </div>
                      @error('length')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
              </div>
              <div class="col-md-3 form-group">
                  <label class="form-label">Width<span class="text-danger">*</span></label>
                  <div class="input-group mb-3">
                      <input type="number" name="width" value="{{old('width',$product->width)}}" class="form-control" placeholder="Width">
                      <span class="input-group-text" id="basic-addon2">mm</span>
                  </div>
                  @error('width')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
              <div class="col-md-3 form-group">
                  <label class="form-label">Height<span class="text-danger">*</span></label>
                  <div class="input-group mb-3">
                      <input type="number" name="height" value="{{old('height',$product->height)}}" class="form-control" placeholder="Height">
                      <span class="input-group-text" id="basic-addon2">mm</span>
                  </div>
                  @error('height')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
              <div class="col-md-3 form-group">
                  <label class="form-label">Weight<span class="text-danger">*</span></label>
                  <div class="input-group mb-3">
                      <input type="number" name="weight" value="{{old('weight',$product->weight)}}" class="form-control" placeholder="Weight">
                      <span class="input-group-text" id="basic-addon2">g</span>
                  </div>
                  @error('weight')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
          </div>
          <div class="form-group input-group mb-3 row">
              <div class="col-md-6">
                  <div class="custom-file">
                      <input id="photo" type="file" class="custom-file-input" name="image" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                      <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                      <div id="holder" style="margin-top:15px;max-height:100px;"></div>
                      @error('photo')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
                  <div class="form-group">
                      <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                      <select name="status" class="form-control">
                          <option value="active" {{(($product->status=='active')? 'selected' : '')}}>Active</option>
                          <option value="inactive" {{(($product->status=='inactive')? 'selected' : '')}}>Inactive</option>
                      </select>
                      @error('status')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
              </div>
              <div class="col-md-6">
                  <img id="preview" src="{{$photo}}" class="rounded mx-auto d-block" width="200px" height="200px" alt="image" style="border: 2px solid cyan">
              </div>
          </div>

        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>
    </div>
    <script>
        document.getElementById('photo').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.width = '200px';
                output.style.height = '200px';
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<script>
    // $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#summary').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
    $(document).ready(function() {
      $('#description').summernote({
        placeholder: "Write detail Description.....",
          tabsize: 2,
          height: 150
      });
    });
</script>

<script>
  var  child_cat_id='{{$product->child_cat_id}}';
        // alert(child_cat_id);
        $('#cat_id').change(function(){
            var cat_id=$(this).val();

            if(cat_id !=null){
                // ajax call
                $.ajax({
                    url:"/admin/category/"+cat_id+"/child",
                    type:"POST",
                    data:{
                        _token:"{{csrf_token()}}"
                    },
                    success:function(response){
                        if(typeof(response)!='object'){
                            response=$.parseJSON(response);
                        }
                        var html_option="<option value=''>--Select any one--</option>";
                        if(response.status){
                            var data=response.data;
                            if(response.data){
                                $('#child_cat_div').removeClass('d-none');
                                $.each(data,function(id,title){
                                    html_option += "<option value='"+id+"' "+(child_cat_id==id ? 'selected ' : '')+">"+title+"</option>";
                                });
                            }
                            else{
                                console.log('no response data');
                            }
                        }
                        else{
                            $('#child_cat_div').addClass('d-none');
                        }
                        $('#child_cat_id').html(html_option);

                    }
                });
            }
            else{

            }

        });
        if(child_cat_id!=null){
            $('#cat_id').change();
        }
</script>
@endpush
