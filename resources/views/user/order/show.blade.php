@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Order Detail </title>
@section('main-content')
<div class="card">
<h5 class="card-header">
    Order
    @if($order->payment_status == 'paid')
    <a href="{{ route('user.order.printInvoice', $order->id) }}" target="_blank" class=" btn btn-sm btn-info shadow-sm float-right ml-2"><i class="fas fa-download fa-sm text-white-50"></i> Print Invoice</a>
    @endif
  </h5>
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
            <th>Action</th>
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
            <td>
                <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                  @csrf
                  @method('delete')
                      <button class="btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>

        </tr>
      </tbody>
    </table>

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
        <div class="row">
          <div class="col-lg-6 col-lx-6">
            <div class="order-info">
              <h4 class="text-center pb-4">ORDER INFORMATION</h4>
              <table class="table">
                    <tr class="">
                        <td>Order Number</td>
                        <td> : {{$order->order_number}}</td>
                    </tr>
                    <tr>
                        <td>Order Date</td>
                        <td> : {{$order->created_at->format('D d M, Y')}} at {{$order->created_at->format('g : i a')}} </td>
                    </tr>
                    <tr>
                        <td>Quantity</td>
                        <td> : {{$order->quantity}}</td>
                    </tr>
                    <tr>
                        <td>Order Status</td>
                        <td> : {{ucwords(str_replace('-', ' ', $order->status))}}</td>
                    </tr>
                    <tr>
                      @php
                          $shipping_charge=DB::table('shippings')->where('id',$order->shipping_id)->pluck('price');
                      @endphp
                        <td>Shipping Charge</td>
                        <td> : Rp. {{number_format($order->shipping->price)}}</td>
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td> : Rp. {{number_format($order->total_amount)}}</td>
                    </tr>
                    <tr>
                      <td>Payment Method</td>
                      <td> : @if($order->payment_method=='bank-transfer') Bank Transfer @else Virtual Account @endif</td>
                    </tr>
                  @if($order->payment_method=='bank-transfer')
                    <tr>
                      <td>Bank Name</td>
                      <td> : {{$order->bank_name}}</td>
                    </tr>
                      <tr>
                          <td>Branch Bank name</td>
                          <td> : {{$order->branch_name}}</td>
                      </tr>
                      <tr>
                          <td>Bank Account Name</td>
                          <td> : {{$order->account_name}}</td>
                      </tr>
                    <tr>
                      <td>Bank Number</td>
                      <td> : {{$order->account_number}}</td>
                    </tr>

                  @endif
                    <tr>
                        <td>Payment Status</td>
                        <td> : {{ucwords(str_replace('-', ' ', $order->payment_status))}}</td>
                    </tr>
                  @if ($order->payment_status == 'unpaid' && \Carbon\Carbon::parse($order->created_at)->diffInHours() < 24)
                      <tr>
                          <td></td>
                          <td>
                              <a href="{{ route('user.order.confirm', $order->id) }}" class="btn-link">Payment Confirmation</a>
                          </td>
                      </tr>
                  @endif
              </table>

            </div>
          </div>

          <div class="col-lg-6 col-lx-6">
            <div class="shipping-info">
              <h4 class="text-center pb-4">SHIPPING INFORMATION</h4>
              <table class="table">
                    <tr class="">
                        <td>Full Name</td>
                        <td> : {{$order->first_name}} {{$order->last_name}}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td> : {{$order->email}}</td>
                    </tr>
                    <tr>
                        <td>Phone No.</td>
                        <td> : {{$order->phone}}</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td> : {{$order->address1}}, {{$order->address2}}</td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td> : {{$order->country}}</td>
                    </tr>
                    <tr>
                        <td>Post Code</td>
                        <td> : {{$order->post_code}}</td>
                    </tr>
              </table>
            </div>
              @if($order->payment_status == 'paid')
                  <div class="shipping-info">
                      <h4 class="text-center pb-4">PAYMENT INFORMATION</h4>
                      <table class="table">
                          <tr class="">
                              <td>Name</td>
                              <td> : {{$order->transaction_name}}</td>
                          </tr>
                          <tr>
                              <td>Transfer Value</td>
                              <td> : Rp. {{number_format($order->transaction_value)}}</td>
                          </tr>
                          <tr>
                              <td>Bank Name</td>
                              <td> : {{$order->transaction_bank}}</td>
                          </tr>
                          <tr>
                              <td>Transfer Date</td>
                              <td> : {{ \Carbon\Carbon::parse($order->transaction_date)->format('d-F-Y') }}</td>
                          </tr>
                          <tr>
                              <td>Number Whatsapp</td>
                              <td> : {{$order->transaction_wa}}</td>
                          </tr>
                          <tr>
                              <td>Transfer File</td>
                              <td> : <img id="preview" width="200px" height="200px" src="{{$photo}}" class="rounded" alt="..."></td>
                          </tr>
                      </table>
                  </div>
              @endif
          </div>

        </div>
      </div>
    </section>
    @endif

  </div>
</div>
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
