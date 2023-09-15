@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Confirmation Payment </title>
@section('main-content')
<div class="card">
<h5 class="card-header">Confirmation Payment</h5>
 <div class="card-body">

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
          <form action="{{route('user.order.confirmStore', $order->id)}}" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="order_id" value="{{$order->id}}">
        <div class="card-body">
            <div class="form-group mb-4">
                <label for="inputTitle" class="col-form-label">Name <span class="text-danger">*</span></label>
                <input id="inputTitle" type="text" name="transaction_name" placeholder="Enter name of bank"  value="{{old('transaction_name')}}" class="form-control">
                @error('transaction_name')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group mb-4">
                <label for="inputTitle" class="col-form-label">Transfer Value <span class="text-danger">*</span></label>
                <input id="inputTitle" type="number" name="transaction_value" placeholder="Enter value transfer"  value="{{old('transaction_value')}}" class="form-control">
                @error('transaction_value')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group mb-4">
                <label for="inputTitle" class="col-form-label">Transfer Date <span class="text-danger">*</span></label>
                <input id="inputTitle" type="date" name="transaction_date" placeholder="Enter date transfer"  value="{{old('transaction_date')}}" class="form-control">
                @error('transaction_date')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group mb-4">
                <label for="inputTitle" class="col-form-label">Bank <span class="text-danger">*</span></label>
                <select class="custom-select" name="transaction_bank" id="inputGroupSelect01">
                    <option selected>Choose...</option>
                    @foreach($bank as $b)
                        <option value="{{$b->bank_name}}">{{$b->bank_name}}</option>
                    @endforeach
                </select>
                @error('transaction_bank')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group mb-4">
                <label for="inputTitle" class="col-form-label">Number Whatsapp <span class="text-danger">*</span></label>
                <input id="inputTitle" type="number" name="transaction_wa" placeholder="Enter number whatsapp"  value="{{old('transaction_wa')}}" class="form-control">
                @error('transaction_wa')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group mb-4">
                <div class="row ml-1">
                <div class="col-md-6 mb-2">
                    <div class="form-group">
                        <input type="file" name="transaction_file" class="custom-file-input" id="customFile" accept=".jpg, .jpeg, .png" required>
                        <label class="custom-file-label" for="customFile">Upload Proof Of Payment</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="text-center">
                        @if(isset($photo))
                        <img id="preview" src="{{$photo}}" class="rounded" alt="...">
                            @else
                        <img id="preview" src="https://fakeimg.pl/200x200/CAEDFF/000/?text=Preview" class="rounded" alt="...">
                        @endif
                    </div>
                </div>
                </div>
            </div>
                <center>
                    <button type="submit" class="btn btn-primary" style="width: 200px">Save</button>
                </center>
        </div>
          </form>
      </div>
    </section>

  </div>
</div>

<script>
    document.getElementById('customFile').addEventListener('change', function(event) {
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
<style>
    .order-info,.shipping-info{
        background:#ECECEC;
        padding:20px;
    }
    .order-info h4,.shipping-info h4{
        text-decoration: underline;
    }

</style>
@endpush


{{--            <div class="form-group mb-4">--}}
{{--                <div class="row">--}}
{{--                    <div class="col-md-6">--}}
{{--                        <label for="inputTitle" class="col-form-label">Bank <span class="text-danger">*</span></label>--}}
{{--                        <select class="custom-select" name="transaction_bank" id="inputGroupSelect01">--}}
{{--                            <option selected>Choose...</option>--}}
{{--                            @foreach($bank as $b)--}}
{{--                                <option value="{{$b->bank_name}}">{{$b->bank_name}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                        @error('title')--}}
{{--                        <span class="text-danger">{{$message}}</span>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
{{--                    <div class="col-md-6">--}}
{{--                        <label for="inputTitle" class="col-form-label">Bank Number <span class="text-danger">*</span></label>--}}
{{--                        <input id="inputTitle" type="number" name="bank_number" placeholder="Enter bank number"  value="{{old('bank_number')}}" class="form-control">--}}
{{--                        @error('bank_number')--}}
{{--                        <span class="text-danger">{{$message}}</span>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
