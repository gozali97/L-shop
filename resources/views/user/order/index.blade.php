@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Order Lists</title>
@section('main-content')

 <!-- DataTales Example -->
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('user.layouts.notification')
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
              <th>Total Amount</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$order->order_number}}</td>
                    <td>{{$order->first_name}} {{$order->last_name}}</td>
                    <td>{{$order->email}}</td>
                    <td>{{$order->quantity}}</td>
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
                        <a href="{{route('user.order.show',$order->id)}}" class="btn btn-warning btn-sm float-left btn-circle mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="view" data-placement="bottom"><i class="fas fa-eye"></i></a>
                        @if(isset($order->no_resi) && $order->status == 'shipping')
                            <a href="{{route('user.order.viewTracking',$order->id)}}" class="btn btn-info btn-sm float-left btn-circle mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="tracking" data-placement="bottom"><i class="fas fa-truck"></i></a>
                        @endif
                        @if($order->status == 'refund-approve'  && $order->refund_status == 'processed')
                            <a href="{{ route('user.order.refund.shipping', $order->id) }}" class="btn btn-success btn-sm float-left btn-circle mr-1" style="height:30px; width:30px;border-radius:50%;" data-toggle="tooltip" title="input waybill" data-placement="bottom"><i class="fas fa-truck-moving"></i></a>
                        @endif
                        @if($order->status == 'completed' && $order->updated_at->diffInDays(now()) < 1)
                            <a href="{{ route('user.order.refund', $order->id) }}" class="btn btn-primary btn-sm float-left btn-circle mr-1" style="height:30px; width:30px;border-radius:50%;" data-toggle="tooltip" title="refund" data-placement="bottom"><i class="fas fa-money-bill"></i></a>
                        @endif
                        @if($order->status == 'shipping')
                        <form action="{{ route('user.order.userConfirmCompleted', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info btn-sm float-left mr-1" style="height:30px; width:30px; border-radius:50%" data-toggle="tooltip" title="confirm" data-placement="bottom" onclick="confirmAction(event)">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        @endif

                            <form method="POST" action="{{route('user.order.delete',[$order->id])}}">
                          @csrf
                          @method('delete')
                              <button class="btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                        </div>
                    </td>
                </tr>
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
@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate{
          display: none;
      }
      .btn-circle {
          display: flex;
          align-items: center;
          justify-content: center;
      }

      .btn-circle i {
          margin: 0;
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
      function confirmAction(event) {
          event.preventDefault();

          Swal.fire({
              title: 'Konfirmasi',
              text: 'Apakah Anda yakin ingin melanjutkan?',
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: 'Ya',
              cancelButtonText: 'Tidak',
          }).then((result) => {
              if (result.isConfirmed) {
                  // Submit the form
                  event.target.closest('form').submit();
              }
          });
      }
  </script>
  <script>

      $('#order-dataTable').DataTable( {
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[7]
                }
            ]
        } );

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
