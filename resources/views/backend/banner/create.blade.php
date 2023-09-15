@extends('backend.layouts.master')

@section('title','E-SHOP || Banner Create')

@section('main-content')
<div class="p-4">
<div class="card">
    <h5 class="card-header">Add App Sliders</h5>
    <div class="card-body">
      <form method="post" action="{{route('banner.store')}}" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{old('title')}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
          <label for="inputDesc" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
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
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                      </select>
                      @error('status')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
                  </div>
              </div>
              <div class="col-md-6">
                  <img id="preview" src="https://fakeimg.pl/400x200/45CFDD/FFF/?text=Preview" class="rounded mx-auto d-block" alt="image" style="border: 2px solid cyan">
              </div>
          </div>
        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Submit</button>
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
            output.style.width = '400px';
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
    $('#lfm').filemanager('image');

    $(document).ready(function() {
    $('#description').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
</script>
@endpush
