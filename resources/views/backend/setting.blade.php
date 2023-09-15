@extends('backend.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Edit Setting</title>
@section('main-content')

 <div class="p-4">
<div class="card">
  <h5 class="card-header">Edit Setting</h5>
  <div class="card-body">
    <form method="post" action="{{route('settings.update')}}" enctype="multipart/form-data">
      @csrf
      {{-- @method('PATCH') --}}
      {{-- {{dd($data)}} --}}
      <div class="form-group">
        <label for="brand_name" class="col-form-label">Brand Name <span class="text-danger">*</span></label>
        <textarea class="form-control" id="brand_name" name="brand_name">{{$data->brand_name}}</textarea>
        @error('brand_name')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="form-group">
        <label for="short_des" class="col-form-label">Short Description <span class="text-danger">*</span></label>
        <textarea class="form-control" id="quote" name="short_des">{{$data->short_des}}</textarea>
        @error('short_des')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="form-group">
        <label for="description" class="col-form-label">Description <span class="text-danger">*</span></label>
        <textarea class="form-control" id="description" name="description">{{$data->description}}</textarea>
        @error('description')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

        <div class="form-group row">
            <div class="col-md-5 ml-3">
                    <input id="brand_logo" type="file" class="custom-file-input" name="brand_logo" aria-describedby="inputGroupFileAddon01">
                    <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                    <div id="holder1" style="margin-top:15px;max-height:100px;"></div>
                    @error('brand_logo')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
            </div>
            <div class="col-md-6 mb-2">
                <img id="preview1" src="{{$brandLogo}}" width="200px" height="200px" class="rounded mx-auto d-block" alt="image" style="border: 2px solid cyan">
            </div>
        </div>
    <div class="form-group row">
    <div class="col-md-5 ml-3">
            <input id="photo" type="file" class="custom-file-input" name="photo" aria-describedby="inputGroupFileAddon01">
            <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
            <div id="holder" style="margin-top:15px;max-height:100px;"></div>

            @error('photo')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <img id="preview2" src="{{$photo}}" width="200px" height="200px" class="rounded mx-auto d-block" alt="image" style="border: 2px solid cyan">
        </div>
    </div>


      <div class="form-group">
        <label for="address" class="col-form-label">Address <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="address" required value="{{$data->address}}">
        @error('address')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="form-group">
        <label for="email" class="col-form-label">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control" name="email" required value="{{$data->email}}">
        @error('email')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="form-group">
        <label for="phone" class="col-form-label">Phone Number <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="phone" required value="{{$data->phone}}">
        @error('phone')
        <span class="text-danger">{{$message}}</span>
        @enderror
      </div>

        <div class="form-group">
            <label class="form-label">WhatsApp Number <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">+62</span>
                <input type="text" class="form-control @error('socmed_wa') is-invalid @enderror" placeholder="82xxxxxxxxx" required name="socmed_wa" value="{{ old('socmed_wa', $data?->socmed_wa) }}">
            </div>
            @error('socmed_wa')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Facebook</label>
            <div class="input-group">
                <span class="input-group-text">https://facebook.com/</span>
                <input type="text" class="form-control @error('socmed_facebook') is-invalid @enderror" required name="socmed_facebook" value="{{ old('socmed_facebook', $data?->socmed_facebook) }}">
            </div>
            @error('socmed_facebook')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Instagram</label>
            <div class="input-group">
                <span class="input-group-text">https://instagram.com/</span>
                <input type="text" class="form-control @error('socmed_instagram') is-invalid @enderror" required name="socmed_instagram" value="{{ old('socmed_instagram', $data?->socmed_instagram) }}">
            </div>
            @error('socmed_instagram')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Theme</label>

            <div class="row">
                {{-- Mengambil data warna tema dari settings.theme yang berupa kolom json --}}
                @foreach($data->theme as $theme => $color)
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">{{ ucwords(str_replace('_', ' ', $theme)) }}</label>
                    <input type="color" class="form-control form-control-color" name="{{ "theme[{$theme}]" }}" value="{{ $color }}" title="Choose theme color">
                </div>
                @endforeach
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
     document.getElementById('brand_logo').addEventListener('change', function(event) {
         var reader = new FileReader();
         reader.onload = function(){
             var output = document.getElementById('preview1');
             output.src = reader.result;
             output.style.width = '200px';
             output.style.height = '200px';
         };
         reader.readAsDataURL(event.target.files[0]);
     });
 </script>
 <script>
     document.getElementById('photo').addEventListener('change', function(event) {
         var reader = new FileReader();
         reader.onload = function(){
             var output = document.getElementById('preview2');
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
  $('#lfm').filemanager('image');
  $('#lfm1').filemanager('image');
  $(document).ready(function() {
    $('#summary').summernote({
      placeholder: "Write short description.....",
      tabsize: 2,
      height: 150
    });
  });
  $(document).ready(function() {
    $('#brand_name').summernote({
      placeholder: "Write brand name",
      tabsize: 2,
      height: 100
    });
  });
  $(document).ready(function() {
    $('#quote').summernote({
      placeholder: "Write short Quote.....",
      tabsize: 2,
      height: 100
    });
  });

  $(document).ready(function() {
    $('#description').summernote({
      placeholder: "Write detail description.....",
      tabsize: 2,
      height: 150
    });
  });
</script>
@endpush
