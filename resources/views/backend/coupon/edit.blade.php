@extends('backend.layouts.master')

@section('main-content')

    <div class="card">
        <h5 class="card-header">Edit Coupon</h5>
        <div class="card-body">
            <form method="post" action="{{ route('coupon.update', $coupon->id) }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="inputName" class="col-form-label">Coupon Name <span class="text-danger">*</span></label>
                    <input id="inputName" type="text" name="coupons_name" placeholder="Enter Coupon Name"
                           value="{{ old('coupons_name', $coupon->coupons_name) }}" class="form-control">
                    @error('coupons_name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputCode" class="col-form-label">Coupon Code <span class="text-danger">*</span></label>
                    <input id="inputCode" type="text" name="code" placeholder="Enter Coupon Code"
                           value="{{ $coupon->code }}" class="form-control">
                    @error('code')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputType" class="col-form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control">
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed
                        </option>
                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>
                            Percentage
                        </option>
                    </select>
                    @error('type')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputValue" class="col-form-label">Value <span class="text-danger">*</span></label>
                    <input id="inputValue" type="number" name="value" placeholder="Enter Coupon value"
                           value="{{ $coupon->value }}" class="form-control">
                    @error('value')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputStartDate" class="col-form-label">Start Date</label>
                    <input id="inputStartDate" type="date" name="start_date" placeholder="Enter Start Date"
                           value="{{ old('start_date', $coupon->start_date) }}" class="form-control">
                    @error('start_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputEndDate" class="col-form-label">End Date</label>
                    <input id="inputEndDate" type="date" name="end_date" placeholder="Enter End Date"
                           value="{{ old('end_date', $coupon->end_date) }}" class="form-control">
                    @error('end_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="inputStatus" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status', $coupon->status) === 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive" {{ old('status', $coupon->status) === 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                    @error('status')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
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
