@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Refund Order </title>
@section('main-content')
<div class="card">
<h5 class="card-header">Refund Order {{$order->order_number}}</h5>
 <div class="card-body">

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
          <form action="{{route('user.order.refundStore', $order->id)}}" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="order_id" value="{{$order->id}}">
        <div class="card-body">
            <div class="mb-2">
                <h5>Product</h5>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td>#</td>
                        <td>Name Product</td>
                        <td>Price</td>
                        <td>Qty</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($product as $p)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$p->title}}</td>
                        <td>Rp. {{number_format($p->price)}}</td>
                        <td>{{$p->quantity}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <hr>
            <h5>Form Refund</h5>
            <div class="row">
            <div class="form-group mb-4 col-md-6">
                <label for="inputTitle" class="col-form-label">Name Costumer<span class="text-danger">*</span></label>
                <input id="inputTitle" type="text" name="nama" placeholder="Enter name of bank"  value="{{$order->first_name}}" class="form-control" readonly>
            </div>
            <div class="form-group mb-4 col-md-6">
                <label for="inputTitle" class="col-form-label">Total Amount <span class="text-danger">*</span></label>
                <input id="inputTitle" type="number" name="amount" placeholder="Enter value transfer"  value="{{$order->sub_total}}" class="form-control" readonly>
            </div>
            </div>
            <div class="form-group mb-4">
                <label for="inputTitle" class="col-form-label">Reason <span class="text-danger">*</span></label>
                <textarea id="inputTitle" name="reason" placeholder="Enter your reason" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group mb-4">
                <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="form-group">
                    <label for="basic-url" class="col-form-label">Upload your request file</label>
                    <input type="file" name="refund_request_file" class="form-control" id="customFile" accept=".jpg, .jpeg, .png" required>
{{--                        <label class="custom-file-label" for="customFile">Upload your request file</label>--}}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="text-center">
                        <img id="preview" src="https://fakeimg.pl/200x200/CAEDFF/000/?text=Preview" class="rounded" alt="...">
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
