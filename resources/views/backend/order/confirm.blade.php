@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Order</title>

@section('main-content')
<div class="card">
<h5 class="card-header">Order</h5>
{{--    <a href="{{route('order.pdf',$order->id)}}" class=" btn btn-sm btn-primary shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i> Generate PDF</a>--}}
  <div class="card-body">
    @if($order)
    <table class="table table-striped table-hover">
      <thead>
        <tr>
            <th>#</th>
            <th>Order No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Quantity</th>
            <th>Charge</th>
            <th>Total Amount</th>
            <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td>{{$order->id}}</td>
            <td>{{$order->order_number}}</td>
            <td>{{$order->first_name}} {{$order->last_name}}</td>
            <td>{{$order->email}}</td>
            <td>{{$order->quantity}}</td>
            <td>Rp. {{number_format($order->shipping->price)}}</td>
            <td>Rp. {{number_format($order->total_amount)}}</td>
            <td>
                @if($order->status=='pending' || $order->status=='return-request' || $order->status=='refund-request')
                    <span class="badge badge-warning">{{ucwords(str_replace('-', ' ', $order->status))}}</span>
                @elseif($order->status=='expired')
                    <span class="badge badge-secondary">{{ucwords($order->status)}}</span>
                @elseif($order->status=='processing' || $order->status=='payment-confirm-request' || $order->status=='return-approve' || $order->status=='refund-approve')
                    <span class="badge badge-primary">{{ucwords(str_replace('-', ' ', $order->status))}}</span>
                @elseif($order->status=='shipping'|| $order->status=='return-shipping')
                    <span class="badge badge-info">{{ucwords(str_replace('-', ' ', $order->status))}}</span>
                @elseif($order->status=='completed' || $order->status=='return-completed')
                    <span class="badge badge-success">{{ucwords(str_replace('-', ' ', $order->status))}}</span>
                @else
                    <span class="badge badge-danger">{{ucwords($order->status)}}</span>
                @endif
            </td>

        </tr>
      </tbody>
    </table>
    <section class="confirmation_part section_padding">
      <div class="order_boxes">
          <form action="{{route('order.confirmStore', $order->id)}}" method="post" enctype="multipart/form-data">
              @csrf
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="col-md-12 mb-3">
                    <div class="text-center">
                        <img id="preview" src="https://fakeimg.pl/400x400/CAEDFF/000/?text=Preview" class="rounded" alt="...">

                    </div>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="form-group">
                        <label for="status">Status :</label>
                        <select name="status" id="" class="form-control">
                            <option value="pending" {{(($order->status=='pending')? 'selected' : '')}}>Pending</option>
                            <option value="expired" {{(($order->status=='expired')? 'selected' : '')}}>Expired</option>
                            <option value="payment-confirm-request" {{(($order->status=='payment-confirm-request')? 'selected' : '')}}>Payment Confirm Request</option>
                            <option value="processing" {{(($order->status=='processing')? 'selected' : '')}}>Processing</option>
                            <option value="shipping" {{(($order->status=='shipping')? 'selected' : '')}}>Shipping</option>
                            <option value="completed" {{(($order->status=='completed')? 'selected' : '')}}>Completed</option>
                            <option value="return-request" {{(($order->status=='return-request')? 'selected' : '')}}>Return Request</option>
                            <option value="return-approve" {{(($order->status=='return-approve')? 'selected' : '')}}>Return Approve</option>
                            <option value="return-cancel" {{(($order->status=='return-cancel')? 'selected' : '')}}>Return Cancel</option>
                            <option value="return-reject" {{(($order->status=='return-reject')? 'selected' : '')}}>Return Reject</option>
                            <option value="return-shipping" {{(($order->status=='return-shipping')? 'selected' : '')}}>Return Shipping</option>
                            <option value="return-completed" {{(($order->status=='return-completed')? 'selected' : '')}}>Return Completed</option>
                            <option value="refund-request" {{(($order->status=='refund-request')? 'selected' : '')}}>Refund Request</option>
                            <option value="refund-approve" {{(($order->status=='refund-approve')? 'selected' : '')}}>Refund Approve</option>
                            <option value="refund-reject" {{(($order->status=='refund-reject')? 'selected' : '')}}>Refund Reject</option>
                        </select>
                    </div>
                </div>
                <center>
                    <button type="submit" class="btn btn-primary">Save</button>
                </center>
            </div>
        </div>
          </form>
      </div>
    </section>
    @endif

  </div>
</div>

<script>
    document.getElementById('customFile').addEventListener('change', function(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview');
            output.src = reader.result;
            output.style.width = '400px';
            output.style.height = '400px';
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


