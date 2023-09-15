@extends('backend.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Order Edit</title>


@section('main-content')
<div class="card">
  <h5 class="card-header">Order Edit</h5>
  <div class="card-body">
    <form action="{{route('order.update',$order->id)}}" method="POST">
      @csrf
      @method('PATCH')
      <div class="form-group">
        <label for="status">Status :</label>
        <select name="status" id="" class="form-control">
          <option value="pending" {{(($order->status=='pending')? 'selected' : '')}}>Pending</option>
          <option value="expired" {{(($order->status=='expired')? 'selected' : '')}}>Expired</option>
          <option value="payment-confirm-request" {{(($order->status=='delivered')? 'selected' : '')}}>Delivered</option>
          <option value="processing" {{(($order->status=='cancel')? 'selected' : '')}}>Processing</option>
          <option value="shipping" {{(($order->status=='cancel')? 'selected' : '')}}>Shipping</option>
          <option value="completed" {{(($order->status=='cancel')? 'selected' : '')}}>Completed</option>
          <option value="return-request" {{(($order->status=='cancel')? 'selected' : '')}}>Return Request</option>
          <option value="return-approve" {{(($order->status=='cancel')? 'selected' : '')}}>Return Approve</option>
          <option value="return-cancel" {{(($order->status=='cancel')? 'selected' : '')}}>Return Cancel</option>
          <option value="return-reject" {{(($order->status=='cancel')? 'selected' : '')}}>Return Reject</option>
          <option value="return-shipping" {{(($order->status=='cancel')? 'selected' : '')}}>Return Shipping</option>
          <option value="return-completed" {{(($order->status=='cancel')? 'selected' : '')}}>Return Completed</option>
          <option value="refund-request" {{(($order->status=='cancel')? 'selected' : '')}}>Refund Request</option>
          <option value="refund-approve" {{(($order->status=='cancel')? 'selected' : '')}}>Refund Approve</option>
          <option value="refund-reject" {{(($order->status=='cancel')? 'selected' : '')}}>Refund Reject</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
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
