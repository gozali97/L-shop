@extends('user.layouts.master')
<title>{{ Helper::getSetting()->brand_name }} - Add Address</title>
@section('main-content')
    <div class="card">
        <h5 class="card-header">Add Address</h5>
        <div class="card-body">
            <form method="post" action="{{route('user.address.store')}}">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="firstname" class="col-form-label">First Name <span class="text-danger">*</span></label>
                            <input id="firstname" type="text" name="firstname" placeholder="Enter firstname"  value="{{old('title')}}" class="form-control">
                            @error('firstname')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="company" class="col-form-label">Company Name<span class="text-danger">*</span></label>
                            <input id="company" type="text" name="company" placeholder="Enter company"  value="{{old('company')}}" class="form-control">
                            @error('company')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone" class="col-form-label">Phone Number <span class="text-danger">*</span></label>
                            <input id="phone" type="text" name="phone" placeholder="Enter phone"  value="{{old('phone')}}" class="form-control">
                            @error('phone')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="city" class="col-form-label">City <span class="text-danger">*</span></label>
                            <select id="city" name="city" class="form-control">
                                <option>-- Select City --</option>
                            </select>
                            @error('city')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="address1" class="col-form-label">Address Line 1<span class="text-danger">*</span></label>
                            <input id="address1" type="text" name="address1" placeholder="Enter address primary"  value="{{old('address1')}}" class="form-control">
                            @error('address1')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="address2" class="col-form-label">Address Line 2<span class="text-danger">*</span></label>
                            <input id="address2" type="text" name="address2" placeholder="Enter address secondary"  value="{{old('address2')}}" class="form-control">
                            @error('address2')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lastname" class="col-form-label">Last Name <span class="text-danger">*</span></label>
                            <input id="lastname" type="text" name="lastname" placeholder="Enter lastname"  value="{{old('lastname')}}" class="form-control">
                            @error('lastname')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-form-label">Email <span class="text-danger">*</span></label>
                            <input id="email" type="email" name="email" placeholder="Enter email"  value="{{old('email')}}" class="form-control">
                            @error('email')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="province" class="col-form-label">Province <span class="text-danger">*</span></label>
                            <select id="province" name="province" class="form-control">
                                <option>-- Select Province --</option>
                                @foreach($dataProvince as $province)
                                    <option value="{{$province['province']}}" data-id="{{$province['province_id']}}">{{$province['province']}}</option>
                                @endforeach
                            </select>
                            @error('province')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="district" class="col-form-label">District <span class="text-danger">*</span></label>
                            <input type="hidden" name="district_id" id="district_id">
                            <select id="district" name="district" class="form-control">
                                <option>-- Select District --</option>
                            </select>
                            @error('district')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="postcode" class="col-form-label">Postal Code<span class="text-danger">*</span></label>
                            <input id="postcode" type="text" name="postcode" placeholder="Enter post code"  value="{{old('postcode')}}" class="form-control">
                            @error('postcode')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="priority" class="col-form-label">Shipping Priority <span class="text-danger">*</span></label>
                            <select id="priority" name="priority" class="form-control">
                                <option value="secondary">Secondary</option>
                                <option value="primary">primary</option>
                            </select>
                            @error('priority')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center items-center">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit" style="margin-left: 1%!important;">Submit</button>
                </div>
            </form>
        </div>
    </div>
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
<script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            $('#province').change(function() {
                var prov_id = $(this).find(':selected').data('id');

                Swal.fire({
                    title: 'Loading...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("user.address.getCity") }}',
                    type: 'GET',
                    data: { prov_id: prov_id },
                    success: function(response) {
                        Swal.close();
                        var cities = response.data;

                        $('#city').empty();
                        $('#district').empty().append('<option value=""> - Select District - </option>');

                        cities.forEach(function(city) {
                            $('#city').append('<option value="' + city.city_name + '" data-id="' + city.city_id + '"> ' + city.city_name + '</option>');
                        });

                        $('#city').trigger('change.select2');
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi masalah saat memuat data kota. Silakan coba lagi nanti.'
                        });
                        console.error(error);
                    }
                });
            });

            $('#city').change(function() {
                var city_id = $(this).find(':selected').data('id');

                Swal.fire({
                    title: 'Loading...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("user.address.getDistrict") }}',
                    type: 'GET',
                    data: { city_id: city_id },
                    success: function(response) {
                        Swal.close();
                        var districts = response.data;

                        $('#district').empty();

                        $('#district_id').val(districts[0].subdistrict_id);

                        districts.forEach(function(district) {
                            $('#district').append('<option value="' + district.type +' '+ district.subdistrict_name +'"' + '" data-id="' + district.subdistrict_id + '">' + district.subdistrict_name + '</option>');
                        });

                        $('#district').trigger('change'); // Trigger the change event to refresh Select2 (if applicable)

                        // Set district_id value based on the selected option's data-id
                        $('#district').change(function() {
                            var selectedDistrictId = $(this).find(':selected').data('id');
                            $('#district_id').val(selectedDistrictId);
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi masalah saat memuat data kota. Silakan coba lagi nanti.'
                        });
                        console.error(error);
                    }
                });
            });
        });
    </script>
