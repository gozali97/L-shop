@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Add My Bank</title>
@section('main-content')
    <div class="card">
        <h5 class="card-header">Add My Bank</h5>
        <div class="card-body">
            <form method="post" action="{{route('user.bank.store')}}">
                {{csrf_field()}}
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
                                @if (old('bank_name'))
                                    {{ array_column($bank, 'bank_name', 'bank_code')[old('bank_name')] }}
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
                    <input id="branch_name" type="text" name="branch_name" placeholder="Enter branch name"  value="{{old('branch_name')}}" class="form-control" required>
                    @error('branch_name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="account_name" class="col-form-label">Account Name <span class="text-danger">*</span></label>
                    <input id="account_name" type="text" name="account_name" placeholder="Enter account name"  value="{{old('account_name')}}" class="form-control" required>
                    @error('account_name')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="account_number" class="col-form-label">Account Number<span class="text-danger">*</span></label>
                    <input id="account_number" type="text" name="account_number" placeholder="Enter account number"  value="{{old('account_number')}}" class="form-control" required>
                    @error('account_number')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="d-flex justify-content-center items-center">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit" style="margin-left: 1%!important;">Submit</button>
                </div>
            </form>
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
<style>
    .order-info,.shipping-info{
        background:#ECECEC;
        padding:20px;
    }
    .order-info h4,.shipping-info h4{
        text-decoration: underline;
    }
    .custom-select.select2-container .select2-selection--single {
        padding: 0.625rem;

    }

    .select2-container {
        width: 100% !important;
    }
</style>
@endpush
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

