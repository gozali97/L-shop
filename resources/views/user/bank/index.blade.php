@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - My Bank</title>
@section('main-content')
 <!-- DataTales Example -->
 <div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('backend.layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Bank Resource</h6>
        @if(isset($data))
        @else
        <a href="{{route('user.bank.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Bank</a>
        @endif
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if(isset($data))
            <form action="{{route('user.bank.update', $data->id)}}" method="post">
                @csrf
                <div class="form-group">
                    <label for="inputBankName" class="col-form-label">
                        Bank Name <span class="text-danger">*</span>
                    </label>
                    <input id="inputBankName" name="bank_name" type="hidden" value="{{ old('bank_name') }}">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-block dropdown-toggle text-left" id="bank" type="button" data-toggle="dropdown"
                                style="border: 1px solid #d1d3e2;
                            border-radius: 0.35rem;">
                            <span id="bank-text">
                                @if (old('bank_name', $data->bank_name))
                                    {{ array_column($bank, 'bank_name', 'bank_code')[old('bank_name', $data->bank_name)] }}
                                @else
                                    Choose bank
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu" style="height: 280px; overflow-y: auto;">
                            <input class="form-control myInput" type="text" placeholder="Search.."
                                   style="padding: 20px;
                                margin-top: -6px;
                                border: 0;
                                border-radius: 0;
                                background: #f1f1f1;">
                            @foreach ($bank as $b)
                                <li><a href="#" class="dropdown-item bank-options" id="{{ $b['bank_code'] }}">{{ $b['bank_name'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    @error('bank_name')
                    <span class="text-danger">{{ $errors->first('bank_name') }}</span>
                    @enderror
                </div>
              <div class="form-group">
                  <label for="branch_name" class="col-form-label">Branch Name<span class="text-danger">*</span></label>
                  <input id="branch_name" type="text" name="branch_name" placeholder="Enter branch name"  value="{{$data->branch_name}}" class="form-control" required>
                  @error('branch_name')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>

              <div class="form-group">
                  <label for="account_name" class="col-form-label">Account Name <span class="text-danger">*</span></label>
                  <input id="account_name" type="text" name="account_name" placeholder="Enter account name"  value="{{$data->account_name}}" class="form-control" required>
                  @error('account_name')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
              <div class="form-group">
                  <label for="account_number" class="col-form-label">Account Number<span class="text-danger">*</span></label>
                  <input id="account_number" type="text" name="account_number" placeholder="Enter account number"  value="{{$data->account_number}}" class="form-control" required>
                  @error('account_number')
                  <span class="text-danger">{{$message}}</span>
                  @enderror
              </div>
              <div class="d-flex justify-content-center items-center">
                  <button class="btn btn-success" type="submit" style="margin-left: 1%!important;">Update</button>
              </div>
          </form>
        @else
          <h6 class="text-center">opening a bank account!!!</h6>
        @endif
      </div>
    </div>
</div>
 <script>
     $(document).ready(function() {
         $('.bank-options').click(function() {
             const btn = document.getElementsByClassName("btn btn-outline-secondary btn-block dropdown-toggle text-left")[0]
             const bank_code = $(this).attr('id')
             $(btn).find('span').text($(this).text())
             document.getElementById("inputBankName").value = bank_code;
         });

         $('.myInput').on('input', function() {
             const searchText = $(this).val().toLowerCase();
             $('.dropdown-menu li').each(function() {
                 const text = $(this).text().toLowerCase();
                 if (text.includes(searchText)) {
                     $(this).show();
                 } else {
                     $(this).hide();
                 }
             });
         });
     });
 </script>


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

      $('#order-dataTable').DataTable( {
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[5,6]
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
