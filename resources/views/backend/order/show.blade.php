@extends('backend.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Order</title>


@section('main-content')
    <style>
        .hide{
            display: none;
        }
    </style>

<div class="p-4">
    <div class="card">
      <h5 class="card-header">
          Order
          <a href="{{ route('order.printInvoice', $order->id) }}" target="_blank" class=" btn btn-sm btn-info shadow-sm float-right ml-2"><i class="fas fa-download fa-sm text-white-50"></i> Print Invoice</a>
          <a href="{{ route('order.PrintItemListPdf', $order->id) }}" target="_blank" class=" btn btn-sm btn-primary shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i> Print Item List</a>
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
                <td>Rp. {{$order->shipping->price}}</td>
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
                    @elseif($order->status=='completed' || $order->status=='return-completed' || $order->status == 'refund-completed')
                        <span class="badge badge-success">{{ucwords(str_replace('-', ' ', $order->status))}}</span>
                    @else
                        <span class="badge badge-danger">{{ucwords($order->status)}}</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group" role="group">
                     <button type="button" class="btn btn-success btn-sm ml-2" data-toggle="modal" data-target="#editStatus{{$order->id}}" style="height:30px; width:30px;border-radius:50%"><i class="fas fa-check"></i></button>
                    <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                      @csrf
                      @method('delete')
                          <button class="btn btn-danger btn-sm ml-2 dltBtn" data-id={{$order->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                    </div>
                </td>

            </tr>
          </tbody>
        </table>

        <section class="confirmation_part section_padding">
          <div class="order_boxes">
            <div class="row">
              <div class="col-lg-6 col-lx-4">
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
                            <td> : {{$order->status}}</td>
                        </tr>
                        <tr>
                            <td>Shipping Charge</td>
                            <td> : Rp. {{number_format($order->shipping->price)}}</td>
                        </tr>
                        <tr>
                          <td>Coupon</td>
                          <td> : Rp. {{number_format($order->coupon)}}</td>
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
                            <td> : {{$order->payment_status}}</td>
                        </tr>
                  </table>
                </div>
                  @if(isset($refund))
                      <form action="{{route('order.refund.finish', $order->id)}}" method="post" enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" name="amount" value="{{$refund->amount}}">
                          <input type="hidden" name="order_id" value="{{$order->id}}">
                          <input type="hidden" name="user_id" value="{{$refund->user_id}}">
                      <div class="shipping-info">
                          <h4 class="text-center pb-4">REFUND INFORMATION</h4>
                          <table class="table">
                              <tr class="">
                                  <td>Name</td>
                                  <td> : {{$order->first_name}}</td>
                              </tr>
                              <tr class="">
                                  <td>Bank Name</td>
                                  <td> : {{$refund->bank_name}}</td>
                              </tr>
                              <tr class="">
                                  <td>Number Bank</td>
                                  <td> : {{$refund->account_number}}</td>
                              </tr>
                              <tr>
                                  <td>Total Amount</td>
                                  <td> : Rp. {{number_format($refund->amount)}}</td>
                              </tr>
                              <tr>
                                  <td>Reason</td>
                                  <td> : {{$refund->reason}}</td>
                              </tr>
                              <tr>
                                  <td>File Request</td>
                                  <td> : <img id="preview" width="200px" height="200px" src="{{$refund_file}}" class="rounded" alt="..."></td>
                              </tr>
                              <tr>
                                  <td>Waybill</td>
                                  <td> : {{$refund->no_resi}}</td>
                              </tr>
                              <tr>
                                  <td>Courir</td>
                                  <td> : {{ucwords($refund->type)}}</td>
                              </tr>
                          </table>
                        @if($refund->status == 'shipping')
                          <div class="form-group mb-4">
                              <label for="basic-url" class="col-form-label">Refund Receipt file</label>
                              <input type="file" name="refund_request_file" class="form-control" id="receipt_file" accept=".jpg, .jpeg, .png" required>
                          </div>
                          <div class="form-group mb-4">
                                  <div class="text-center">
                                      <img id="preview3" src="https://fakeimg.pl/200x200/CAEDFF/000/?text=Preview" class="rounded" alt="...">
                                  </div>
                          </div>
                          <div class="form-group">
                              <center>
                              <button type="submit" class="btn btn-primary" style="width: 200px">Save</button>
                              </center>
                          </div>
                          @endif
                      </div>
                      </form>
                  @endif
              </div>

              <div class="col-lg-6 col-lx-4">
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
                            <td>Postal Code</td>
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
                  @if($order->status == 'refund-request')
                      <div class="shipping-info">
                          <h4 class="text-center pb-4">REFUND INFORMATION</h4>
                          <table class="table">
                              <tr class="">
                                  <td>Name</td>
                                  <td> : {{$order->first_name}}</td>
                              </tr>
                              <tr>
                                  <td>Total Amount</td>
                                  <td> : Rp. {{number_format($order->ref_amount)}}</td>
                              </tr>
                              <tr>
                                  <td>Reason</td>
                                  <td> : {{$order->reason}}</td>
                              </tr>
                              <tr>
                                  <td>File Request</td>
                                  <td> : <img id="preview" width="200px" height="200px" src="{{$ref_file}}" class="rounded" alt="..."></td>
                              </tr>
                              <tr>
                                  <td></td>
                                  <td><button type="button" class="btn btn-success btn-sm ml-2" data-toggle="modal" data-target="#editRefund{{$order->id}}" >Confirm Refund</button>
                                  </td>
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
</div>

    <div class="modal fade" id="editStatus{{$order->id}}" tabindex="-1" aria-labelledby="addResiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{route('order.confirmStore',$order->id)}}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addResiLabel">Edit Status Order {{$order->order_number}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status">Status :</label>
                            <select name="status" id="status" class="form-control">
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
                                <option value="refund-rejected" {{(($order->status=='refund-rejected')? 'selected' : '')}}>Refund Reject</option>
                                <option value="refund-completed" {{(($order->status=='refund-completed')? 'selected' : '')}}>Refund Completed</option>
                            </select>
                        </div>

                        <div id="reason" class="form-group hide">
                            <label for="status">Reason :</label>
                            <select name="note" id="note" class="form-control">
                                <option value="not-valid">Proof of Transfer is invalid/unclear</option>
                                <option value="min-value">Tender amount are not match</option>
                                <option value="not-received">Funds have not yet settle on our account</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editRefund{{$order->id}}" tabindex="-1" aria-labelledby="addResiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{route('order.confirmRefund',$order->id)}}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addResiLabel">Confirm Refund Order {{$order->order_number}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status">Status :</label>
                            <select name="status2" id="status2" class="form-control">
                                @foreach (['refund-request', 'refund-approve', 'refund-rejected', 'refund-completed'] as $status)
                                    <option value="{{ $status }}" {{ ($order->status == $status) ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('-', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="reason" class="form-group hide">
                            <label for="status">Reason :</label>
                            <select name="note2" id="note2" class="form-control">
                                <option value="not-valid">Proof of Transfer is invalid/unclear</option>
                                <option value="min-value">Tender amount are not match</option>
                                <option value="not-received">Funds have not yet settle on our account</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var statusSelect = document.getElementById('status');
            var reasonDiv = document.getElementById('reason');

            statusSelect.addEventListener('change', function() {
                if (statusSelect.value === 'pending') {
                    reasonDiv.classList.remove('hide');
                } else {
                    reasonDiv.classList.add('hide');
                }
            });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var statusSelect2 = document.getElementById('status2');
            var reasonDiv2 = document.getElementById('reason2');

            statusSelect2.addEventListener('change', function() {
                if (statusSelect2.value === 'refund-rejected') {
                    reasonDiv2.classList.remove('hide');
                } else {
                    reasonDiv2.classList.add('hide');
                }
            });
        });

    </script>

    <script>
        document.getElementById('receipt_file').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview3');
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
