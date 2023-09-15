@extends('backend.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Shipping Lists</title>
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
             <h6 class="m-0 font-weight-bold text-primary float-left">Shipping List</h6>
         </div>
         <div class="card-body">
             <form method="post" action="{{route('shipping.update', $shipping->id)}}">
                 {{csrf_field()}}
                 <div class="row">
                     <div class="col-md-8">
                         <div class="mt-2">
                             <label for="inputTitle" class="col-form-label">Amount <span class="text-danger">*</span></label>
                             <input id="inputTitle" type="number" name="amount" placeholder="Enter amount"  value="{{$shipping->amount}}" class="form-control" required>
                             @error('type')
                             <span class="text-danger">{{$message}}</span>
                             @enderror
                         </div>
                     </div>
                     <div class="col-md-2">
                         <div class="mt-2">
                             <label for="status" class="col-form-label">Type Ammount <span class="text-danger">*</span></label>
                             <select name="type" class="form-control"  required>
                                 <option value="">- Pilih type amount -</option>
                                 <option value="fixed" {{$shipping->amount_type == 'fixed' ? 'selected':''}}>Fixed Amount</option>
                                 <option value="persen" {{$shipping->amount_type == 'persen' ? 'selected':''}}>Percentage</option>
                             </select>
                             @error('status')
                             <span class="text-danger">{{$message}}</span>
                             @enderror
                         </div>
                     </div>
                     <div class="col-md-2">
                         <button type="submit" class="btn btn-primary mt-5">Save</button>
                     </div>
                 </div>
                 <div class="row p-4">
                     @foreach($data as $key => $val)
                         <div class="col-md-3">
                             <div class="row mt-5">
                                 <div class="col-md-3 mt-1">
                                     <label class="text-primary">{{ strtoupper($key)}}</label>
                                 </div>
                                 <div class="col-md-1">
                                     <label class="switch">
                                         <input name="status[{{$key}}]" {{$val == true ? 'checked': ''}} type="checkbox">
                                         <span class="slider round"></span>
                                     </label>
                                 </div>
                             </div>
                         </div>
                     @endforeach
                 </div>
             </form>
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
      .zoom {
        transition: transform .2s; /* Animation */
      }

      .zoom:hover {
        transform: scale(3.2);
      }

      .switch {
          position: relative;
          display: inline-block;
          width: 60px;
          height: 34px;
      }

      .switch input {
          opacity: 0;
          width: 0;
          height: 0;
      }

      .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
      }

      .slider:before {
          position: absolute;
          content: "";
          height: 26px;
          width: 26px;
          left: 4px;
          bottom: 4px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
      }

      input:checked + .slider {
          background-color: #2196F3;
      }

      input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
      }

      input:checked + .slider:before {
          -webkit-transform: translateX(26px);
          -ms-transform: translateX(26px);
          transform: translateX(26px);
      }

      /* Rounded sliders */
      .slider.round {
          border-radius: 34px;
      }

      .slider.round:before {
          border-radius: 50%;
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

      $('#banner-dataTable').DataTable( {
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[3,4]
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
