@extends('backend.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Order Lists</title>

@section('main-content')
 <!-- DataTales Example -->
 <div class="p-4">
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Order Lists</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(count($orders)>0)
        <table class="table table-bordered" id="order-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>#</th>
              <th>Order No.</th>
              <th>Name</th>
              <th>Email</th>
              <th>Qty</th>
{{--              <th>Charge</th>--}}
              <th>Total Amount</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
            @php
                $shipping_charge=DB::table('shippings')->where('id',$order->shipping_id)->pluck('price');
            @endphp
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$order->order_number}}</td>
                    <td>{{$order->first_name}} {{$order->last_name}}</td>
                    <td>{{$order->email}}</td>
                    <td>{{$order->quantity}}</td>
{{--                    <td>@foreach($shipping_charge as $data) Rp.  {{number_format($data)}} @endforeach</td>--}}
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
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-edit"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="{{route('order.show',$order->id)}}" class="btn dropdown-item btn-warning btn-sm float-left mr-1" data-toggle="tooltip" title="view" data-placement="bottom"><i class="fas fa-eye me-2"></i> View</a>
{{--                                @if($order->canConfirmPayment())--}}
{{--                                <a href="{{route('order.confirm',$order->id)}}" class="btn dropdown-item btn-warning btn-sm float-left mr-1" data-toggle="tooltip" title="view" data-placement="bottom"><i class="fas fa-wallet me-2"></i> Confirm Payment</a>--}}
{{--                                @endif--}}
                                {{--                        <a href="" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>--}}
                                <!-- Button trigger modal edit status-->
                                <button type="button" class="dropdown-item btn btn-primary btn-sm float-left mr-1" data-toggle="modal" data-target="#editStatus{{$order->id}}">
                                    <i class="fas fa-edit"></i>
                                    Change Status
                                </button>
                                <!-- Button trigger modal resi-->
                                @if($order->canAddWaybill())
                                <button type="button" class="dropdown-item btn btn-info btn-sm float-left mr-1" data-toggle="modal" data-target="#addResi{{$order->id}}">
                                    <i class="fas fa-plus me-2"></i>
                                    Add Waybill
                                </button>
                                @endif
                                @if(isset($order->no_resi))
                                    <a href="{{route('order.viewTracking',$order->id)}}" class="dropdown-item btn btn-primary btn-sm float-left mr-1" data-toggle="tooltip" title="tracking" data-placement="bottom"><i class="fas fa-truck me-2"></i> Tracking</a>
                                @endif
                                <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                                    @csrf
                                    @method('delete')
                                    <button class="dropdown-item btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:30px; width:30px;border-radius:50%;" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt me-2"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            <!-- Modal Edit Status-->
            <div class="modal fade" id="editStatus{{$order->id}}" tabindex="-1" aria-labelledby="addResiLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{route('order.update',$order->id)}}" method="POST">
                        @csrf
                        @method('PATCH')
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
                                        <option value="refund-rejected" {{(($order->status=='refund-rejected')? 'selected' : '')}}>Refund Reject</option>
                                        <option value="refund-completed" {{(($order->status=='refund-completed')? 'selected' : '')}}>Refund Completed</option>
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

            <!-- Modal Resi-->
            <div class="modal fade" id="addResi{{$order->id}}" tabindex="-1" aria-labelledby="addResiLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{route('order.addResi', $order->shipping_id)}}" method="POST">
                        @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addResiLabel">Input Waybill {{$order->order_number}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="no_resi">No Resi</label>
                                <input type="text" class="form-control" name="no_resi" id="no_resi" value="{{$order->no_resi}}" placeholder="No Resi">
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
            @endforeach
          </tbody>
        </table>
{{--        <span style="float:right">{{$orders->links()}}</span>--}}
        @else
          <h6 class="text-center">No orders found!!! Please order some products</h6>
        @endif
      </div>
    </div>
</div>
 </div>
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate{
          display: none;
      }
  </style>
@endpush

@push('scripts')

  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>

      $('#order-dataTable').DataTable({
          "scrollX": false,
          "columnDefs": [
              {
                  "orderable": false,
                  "targets": [6], // Only the "Action" column is non-orderable
              }
          ]
      });

        // Sweet alert

        function deleteData(id){

        }
  </script>
  <script>
      $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
          $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
              var dataID=$(this).data('id');
              // alert(dataID);
              e.preventDefault();
              swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this data!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                       form.submit();
                    } else {
                        swal("Your data is safe!");
                    }
                });
          })
      })
  </script>
@endpush
