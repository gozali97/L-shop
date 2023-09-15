@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Refund Send Shipping </title>
@section('main-content')
<div class="card">
<h5 class="card-header">Input Waybill Refund Order {{$order->order_number}}</h5>
 <div class="card-body">

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
          <form action="{{route('user.order.refund.shippingStore', $order->id)}}" method="post" enctype="multipart/form-data">
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
            <h5>Form Detail Shipping Refund</h5>
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
            @if($haveShipping)
                <div class="form-group mb-4">
                <label for="courir" class="col-form-label">Courir <span class="text-danger">*</span></label>
                <input id="courir" name="courir" value="{{$haveShipping->type}}" class="form-control" readonly>
            </div>
            <div class="form-group mb-4">
                <label for="service" class="col-form-label">Service <span class="text-danger">*</span></label>
                <input id="service" type="text" value="{{$haveShipping->service}}" class="form-control" readonly>
            </div>
            <div class="form-group mb-4">
                <label for="no_resi" class="col-form-label">Waybill <span class="text-danger">*</span></label>
                <input id="no_resi" type="text" value="{{$haveShipping->no_resi}}" placeholder="Enter waybill" class="form-control" readonly>
            </div>
            <div class="form-group mb-4">
                <label for="price_shipping" class="col-form-label">Price Shipping <span class="text-danger">*</span></label>
                <input id="price_shipping" type="number" value="{{$haveShipping->price}}" placeholder="Enter waybill" class="form-control" readonly>
            </div>
            @else
                <div class="form-group mb-4">
                    <label for="courir" class="col-form-label">Courir <span class="text-danger">*</span></label>
                    <select id="courir" name="courir" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        @foreach ($shipping as $key => $value)
                            <option value="{{ $key }}">{{ ucwords($key) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label for="service" class="col-form-label">Service <span class="text-danger">*</span></label>
                    <input id="service" type="text" name="service" placeholder="Enter service example (REG)" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label for="no_resi" class="col-form-label">Waybill <span class="text-danger">*</span></label>
                    <input id="no_resi" type="text" name="no_resi" placeholder="Enter waybill" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label for="price_shipping" class="col-form-label">Price Shipping <span class="text-danger">*</span></label>
                    <input id="price_shipping" type="number" name="price_shipping" placeholder="Enter waybill" class="form-control" required>
                </div>
                <center>
                    <button type="submit" class="btn btn-primary" style="width: 200px">Save</button>
                </center>
            @endif

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
