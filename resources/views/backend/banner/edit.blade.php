@extends('backend.layouts.master')
@section('title','E-SHOP || Banner Edit')
@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Banner</h5>
    <div class="card-body">
      <form method="post" action="{{route('banner.update',$banner->id)}}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{$banner->title}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
          <label for="inputDesc" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{$banner->description}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
        <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
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
                            <option value="active" {{(($banner->status=='active') ? 'selected' : '')}}>Active</option>
                            <option value="inactive" {{(($banner->status=='inactive') ? 'selected' : '')}}>Inactive</option>
                        </select>
                        @error('status')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <img id="preview" src="{{$photo}}" width="400px" height="200px" class="rounded mx-auto d-block" alt="image" style="border: 2px solid cyan">
                </div>
            </div>

        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
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
