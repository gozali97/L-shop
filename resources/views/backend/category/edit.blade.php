@extends('backend.layouts.master')

@section('main-content')
<div class="p-4">
<div class="card">
    <h5 class="card-header">Edit Category</h5>
    <div class="card-body">
      <form method="post" action="{{route('category.update',$category->id)}}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{$category->title}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">Summary</label>
          <textarea class="form-control" id="summary" name="summary">{{$category->summary}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="is_parent">Is Parent</label><br>
          <input type="checkbox" name='is_parent' id='is_parent' value='{{$category->is_parent}}' {{(($category->is_parent==1)? 'checked' : '')}}> Yes
        </div>
        {{-- {{$parent_cats}} --}}
        {{-- {{$category}} --}}

      <div class="form-group {{(($category->is_parent==1) ? 'd-none' : '')}}" id='parent_cat_div'>
          <label for="parent_id">Parent Category</label>
          <select name="parent_id" class="form-control">
              <option value="">--Select any category--</option>
              @foreach($parent_cats as $key=>$parent_cat)

                  <option value='{{$parent_cat->id}}' {{(($parent_cat->id==$category->parent_id) ? 'selected' : '')}}>{{$parent_cat->title}}</option>
              @endforeach
          </select>
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
                          <option value="active" {{(($category->status=='active')? 'selected' : '')}}>Active</option>
                          <option value="inactive" {{(($category->status=='inactive')? 'selected' : '')}}>Inactive</option>
                      </select>
                      @error('status')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
              </div>
              <div class="col-md-6">
                  <img id="preview" src="{{$photo}}" class="rounded mx-auto d-block" alt="image" style="border: 2px solid cyan">
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
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    // $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#summary').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
</script>
<script>
  $('#is_parent').change(function(){
    var is_checked=$('#is_parent').prop('checked');
    // alert(is_checked);
    if(is_checked){
      $('#parent_cat_div').addClass('d-none');
      $('#parent_cat_div').val('');
    }
    else{
      $('#parent_cat_div').removeClass('d-none');
    }
  })
</script>
@endpush
