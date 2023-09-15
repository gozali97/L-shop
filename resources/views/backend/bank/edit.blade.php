@extends('backend.layouts.master')

@section('main-content')

    <div class="p-4">
    <div class="card">
        <h5 class="card-header">Edit Bank Account</h5>
        <div class="card-body">
            <form method="post" action="{{route('bank.update', $bank->id)}}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="inputBankName" class="col-form-label">
                        Bank Name <span class="text-danger">*</span>
                    </label>
                    <input id="inputBankName" name="bank_name" type="hidden" value="{{ old('bank_name', $bank->bank_name) }}">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-block dropdown-toggle text-left" id="bank" type="button" data-toggle="dropdown"
                            style="border: 1px solid #d1d3e2;
                            border-radius: 0.35rem;">
                            <span id="bank-text">
                                @if (old('bank_name', $bank->bank_name))
                                    {{ array_column($bank_list, 'bank_name', 'bank_code')[old('bank_name', $bank->bank_name)] }}
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
                        @foreach ($bank_list as $item)
                            <li><a href="#" class="dropdown-item bank-options" id="{{ $item['bank_code'] }}">{{ $item['bank_name'] }}</a></li>
                        @endforeach
                        </ul>
                    </div>
                    @error('bank_name')
                    <span class="text-danger">{{ $errors->first('bank_name') }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="inputBranchName" class="col-form-label">
                        Branch Name <span class="text-danger">*</span>
                    </label>
                    <input id="inputBranchName"
                           type="text"
                           name="branch_name"
                           placeholder="Enter branch name"
                           value="{{ old('branch_name', $bank->branch_name) }}"
                           class="form-control">
                    @error('branch_name')
                    <span class="text-danger">{{ $errors->first('branch_name') }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputAccountName" class="col-form-label">
                        Account Name <span class="text-danger">*</span>
                    </label>
                    <input id="inputAccountName"
                           type="text"
                           name="account_name"
                           placeholder="Enter account name"
                           value="{{ old('account_name', $bank->account_name) }}"
                           class="form-control">
                    @error('account_name')
                    <span class="text-danger">{{ $errors->first('account_name') }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputAccountNumber" class="col-form-label">
                        Account Number <span class="text-danger">*</span>
                    </label>
                    <input id="inputAccountNumber"
                           type="text"
                           name="account_number"
                           placeholder="Enter account number"
                           value="{{ old('account_number', $bank->account_number) }}"
                           class="form-control">
                    @error('account_number')
                    <span class="text-danger">{{ $errors->first('account_number') }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="form-group input-group mb-3 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control">
                                    <option value="active" {{(($bank->status=='active') ? 'selected' : '')}}>Active</option>
                                    <option value="inactive" {{(($bank->status=='inactive') ? 'selected' : '')}}>Inactive</option>
                                </select>
                                @error('status')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    {{-- <script>
        document.getElementById('photo').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.width = '400px';
                output.style.height = '200px';
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    </script> --}}
@endsection

@push('scripts')
    <script>
        $(document).ready(function(){
            $(".myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                console.log(value);
                $(".dropdown-menu li").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        $('.bank-options').click(function() {
            const btn = document.getElementsByClassName("btn btn-outline-secondary btn-block dropdown-toggle text-left")[0]
            const bank_code = $(this).attr('id')
            $(btn).find('span').text($(this).text())
            document.getElementById("inputBankName").value = bank_code;
        })
    </script>
@endpush
