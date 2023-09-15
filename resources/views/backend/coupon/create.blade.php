@extends('backend.layouts.master')

@section('main-content')

    <div class="card">
        <h5 class="card-header">Add Coupon</h5>
        <div class="card-body">
            <form method="post" action="{{ route('coupon.store') }}">
                @csrf
                <div class="form-group">
                    <label for="inputName" class="col-form-label">Coupon Name <span class="text-danger">*</span></label>
                    <input id="inputName" type="text" name="coupons_name" placeholder="Enter Coupon Name"
                           value="{{ old('coupons_name') }}" class="form-control">
                    @error('coupons_name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Coupon Code <span
                            class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="code" placeholder="Enter Coupon Code"
                           value="{{ old('code') }}" class="form-control">
                    @error('code')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type" class="col-form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control">
                        <option value="fixed">Fixed</option>
                        <option value="percent">Percent</option>
                    </select>
                    @error('type')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Value <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="number" name="value" placeholder="Enter Coupon value"
                           value="{{ old('value') }}" class="form-control">
                    @error('value')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputStartDate" class="col-form-label">Start Date</label>
                    <input id="inputStartDate" type="date" name="start_date" placeholder="Enter Coupon Name"
                           value="{{ old('start_date') }}" class="form-control">
                    @error('start_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputEndDate" class="col-form-label">End Date</label>
                    <input id="inputEndDate" type="date" name="end_date" placeholder="Enter Coupon Name"
                           value="{{ old('end_date') }}" class="form-control">
                    @error('end_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('status')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
    <script>
        $('#lfm').filemanager('image');

        $(document).ready(function () {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });
        });
    </script>
@endpush

