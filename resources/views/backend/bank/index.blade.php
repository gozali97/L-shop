@extends('backend.layouts.master')

@section('main-content')
    <div class="p-3">
        <div class="card shadow mb-4">
            <div class="row">
                <div class="col-md-12">
                    @include('backend.layouts.notification')
                </div>
            </div>
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary float-left">Bank Account Lists</h6>
                <a href="{{route('bank.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
                   data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Bank Account</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @if(count($banks)>0)
                        <table class="table table-bordered" id="bank-dataTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Bank Name</th>
                                <th>Branch Name</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($banks as $bank)
                                <tr>
                                    <td>{{ $bank->id }}</td>
                                    <td>{{ array_column($bank_list, 'bank_name', 'bank_code')[$bank->bank_name] }}</td>
                                    <td>{{ $bank->branch_name }}</td>
                                    <td>{{ $bank->account_name }}</td>
                                    <td>{{ $bank->account_number }}</td>
                                    <td>
                                        @if($bank->status=='active')
                                            <span class="badge badge-success">{{$bank->status}}</span>
                                        @else
                                            <span class="badge badge-warning">{{$bank->status}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('bank.edit', $bank->id) }}"
                                           class="btn btn-primary btn-sm float-left mr-1"
                                           style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                           title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                        <form method="POST" action="{{route('bank.destroy', [$bank->id])}}">
                                            @method('DELETE')
                                            @csrf
                                            <button class="btn btn-danger btn-sm dltBtn" data-id="{{$bank->id}}"
                                                    style="height:30px; width:30px;border-radius:50%"
                                                    data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <span style="float:right">{{ $banks->links() }}</span>
                    @else
                        <h6 class="text-center">No Banks found!!! Please create Bank account</h6>
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
        /* .payment-bank-logo {
            background-color: #0c5460;
            width: 80px;
            height: 53px;
            margin: 5px 5px 0 5px;
            background-size: cover;
            background-position: center;
        } */
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
        $('#bank-dataTable').DataTable({
            "scrollX": false,
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[2]
                }
            ]
        });
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.dltBtn').click(function (e) {
                const form = $(this).closest('form');
                e.preventDefault();
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this data!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
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
