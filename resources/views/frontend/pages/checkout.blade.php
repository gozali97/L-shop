@extends('frontend.layouts.master')
@section('title', Helper::getSetting()->brand_name . ' - Checkout')
@section('main-content')

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Checkout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Checkout -->
    <section class="shop checkout section">
        <div class="container">
            <form class="form" method="POST" action="{{ route('cart.order') }}">
                @csrf
                <div class="row">

                    <div class="col-lg-8 col-12">
                        <div class="checkout-form">
                            <h2>Make Your Checkout Here</h2>
                            <p>Please register in order to checkout more quickly</p>
                            <!-- Form -->
                            @if (isset($address))
                                <div class="card">
                                    <div class="card-header">
                                        Shipping Details
                                        <a href="{{ route('user.address.index') }}"
                                           class="float-right text-primary">Edit</a>
                                    </div>

                                    <div class="card-body row">
                                        <div class="col-md-6">
                                            <p><span class="text-bold">Name </span><br>{{ $address->first_name }}
                                                {{ $address->last_name }}</p>
                                            <p class="mt-0"><span class="text-bold">Phone Number </span><br>
                                                {{ $address->contact_phone }}</p>
                                            <p><span class="text-bold">Email </span><br> {{ $address->contact_email }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><span class="text-bold">Address </span><br>
                                                {{ $address->address }}<br>
                                                {{ $address->district }}<br>
                                                {{ $address->city }}<br>
                                                {{ $address->state }}<br>
                                                {{ $address->postcode }}
                                            </p>
                                        </div>
                                        <input type="hidden" name="firstname" value="{{ $address->first_name }}">
                                        <input type="hidden" name="lastname" value="{{ $address->last_name }}">
                                        <input type="hidden" name="phone" value="{{ $address->contact_phone }}">
                                        <input type="hidden" name="email" value="{{ $address->contact_email }}">
                                        <input type="hidden" name="address1" value="{{ $address->address }}">
                                        <input type="hidden" name="address2" value="{{ $address->address2 }}">
                                        <input type="hidden" name="postcode" value="{{ $address->postcode }}">
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname" class="col-form-label">First Name <span
                                                    class="text-danger">*</span></label>
                                            <input id="firstname" type="text" name="firstname"
                                                   placeholder="Enter firstname" value="{{ old('title') }}"
                                                   class="form-control" required>
                                            @error('firstname')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="company" class="col-form-label">Company Name<span
                                                    class="text-danger">*</span></label>
                                            <input id="company" type="text" name="company" placeholder="Enter company"
                                                   value="{{ old('company') }}" class="form-control" required>
                                            @error('company')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="phone" class="col-form-label">Phone Number <span
                                                    class="text-danger">*</span></label>
                                            <input id="phone" type="number" name="phone" placeholder="Enter phone"
                                                   value="{{ old('phone') }}" class="form-control" required>
                                            @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="city" class="col-form-label">City <span
                                                    class="text-danger">*</span></label>
                                            <select id="city" name="city" required>
                                                <option>-- Select City --</option>
                                            </select>
                                            @error('city')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label for="address1" class="col-form-label">Address Line 1<span
                                                    class="text-danger">*</span></label>
                                            <input id="address1" type="text" name="address1"
                                                   placeholder="Enter address primary" value="{{ old('address1') }}"
                                                   class="form-control" required>
                                            @error('address1')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastname" class="col-form-label">Last Name <span
                                                    class="text-danger">*</span></label>
                                            <input id="lastname" type="text" name="lastname"
                                                   placeholder="Enter lastname" value="{{ old('lastname') }}"
                                                   class="form-control" required>
                                            @error('lastname')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="email" class="col-form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input id="email" type="email" name="email" placeholder="Enter email"
                                                   value="{{ old('email') }}" class="form-control" required>
                                            @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="province" class="col-form-label">Province <span
                                                    class="text-danger">*</span></label>
                                            <select id="province" name="province" required>
                                                <option>-- Select Province --</option>
                                                @foreach ($dataProvince as $province)
                                                    <option value="{{ $province['province'] }}"
                                                            data-id="{{ $province['province_id'] }}">
                                                        {{ $province['province'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('province')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="district" class="col-form-label">District <span
                                                    class="text-danger">*</span></label>
                                            <input type="hidden" name="district_id" id="district_id">
                                            <select id="district" name="district" required>
                                                <option>-- Select District --</option>
                                            </select>
                                            @error('district')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="postcode" class="col-form-label">Postal Code<span
                                                    class="text-danger">*</span></label>
                                            <input id="postcode" type="number" name="postcode"
                                                   placeholder="Enter post code" value="{{ old('postcode') }}"
                                                   class="form-control" required>
                                            @error('postcode')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!--/ End Form -->
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="order-details">
                            <!-- Order Widget -->
                            <div class="single-widget">
                                <h2>CART TOTALS</h2>
                                <div class="content">
                                    <ul>
                                        <li class="order_subtotal" data-price="{{ Helper::totalCartPrice() }}">Cart
                                            Subtotal<span>Rp. {{ number_format(Helper::totalCartPrice()) }}</span></li>
                                        <li class="shipping">
                                            Shipping Cost
                                            <div class="px-3" id="shipping">
                                                @if($shippingCosts != '')
                                                    <select name="shipping" class="form-control mt-2" required>
                                                        <option value="">Select Shipping</option>
                                                        @foreach ($shippingCosts as $cost)
                                                            <option
                                                                value="{{ $cost['name'] }}-{{ $cost['type'] }}-{{ $cost['price'] }}"
                                                                class="shippingOption" data-price="">
                                                                {{ $cost['name'] }} - {{ $cost['type'] }}
                                                                : {{ $cost['price'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="name" value="{{ $cost['name'] }}">
                                                    <input type="hidden" name="service" value="{{ $cost['type'] }}">
                                                    <input type="hidden" name="cost" value="{{ $cost['price'] }}">
                                                @endif
                                            </div>
                                        </li>
                                        @if (session('coupon'))
                                            <li class="coupon_price" data-price="{{ session('coupon')['value'] }}">You
                                                Save<span>Rp. {{ number_format(session('coupon')['value']) }}</span>
                                            </li>
                                        @endif
                                        @php
                                            $total_amount = Helper::totalCartPrice();
                                            if (session('coupon')) {
                                                $total_amount = $total_amount - session('coupon')['value'];
                                            }
                                        @endphp
                                        @if (session('coupon'))
                                            <li class="last" id="order_total_price">
                                                Total<span>Rp. {{ number_format($total_amount) }}</span></li>
                                        @else
                                            <li class="last" id="order_total_price">
                                                Total<span>Rp. {{ number_format($total_amount) }}</span></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <!--/ End Order Widget -->
                            <!-- Order Widget -->
                            <div class="single-widget">
                                <h2>Payments</h2>
                                <div class="content">
                                    <div class="checkbox">
                                        {{-- <label class="checkbox-inline" for="1"><input name="updates" id="1" type="checkbox"> Check Payments</label> --}}
                                        <form-group>
                                            <div class="row">
                                                @foreach($bankAccounts as $bank)
                                                    <div class="col-6">
                                                        <input
                                                            name="payment_method"
                                                            type="radio"
                                                            class="mr-3"
                                                            value="{{ $bank->id }}"
                                                            required>
                                                        <label> {{ $bank->bank_name }}</label><br>
                                                    </div>
                                                @endforeach
                                            </div>
                                            {{--                                            <input name="payment_method" type="radio" value="paypal" required> <label>--}}
                                            {{--                                                PayPal</label>--}}
                                        </form-group>

                                    </div>
                                </div>
                            </div>
                            <!--/ End Order Widget -->
                            <!-- Payment Method Widget -->
                            <div class="single-widget payement">
                                <div class="content">
                                    <img src="{{ '/backend/img/payment-method.png' }}" alt="#">
                                </div>
                            </div>
                            <!--/ End Payment Method Widget -->
                            <!-- Button Widget -->
                            <div class="single-widget get-button">
                                <div class="content">
                                    <div class="button">
                                        <button type="submit" class="btn">proceed to checkout</button>
                                    </div>
                                </div>
                            </div>
                            <!--/ End Button Widget -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!--/ End Checkout -->

    <!-- Start Shop Services Area  -->
    <section class="shop-services section home">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-rocket"></i>
                        <h4>Free shiping</h4>
                        <p>Orders over $100</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-reload"></i>
                        <h4>Free Return</h4>
                        <p>Within 30 days returns</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-lock"></i>
                        <h4>Sucure Payment</h4>
                        <p>100% secure payment</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-tag"></i>
                        <h4>Best Peice</h4>
                        <p>Guaranteed price</p>
                    </div>
                    <!-- End Single Service -->
                </div>
            </div>
        </div>
    </section>
    <!-- End Shop Services -->

    <!-- Start Shop Newsletter  -->
    <section class="shop-newsletter section">
        <div class="container">
            <div class="inner-top">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2 col-12">
                        <!-- Start Newsletter Inner -->
                        <div class="inner">
                            <h4>Newsletter</h4>
                            <p> Subscribe to our newsletter and get <span>10%</span> off your first purchase</p>
                            <form action="mail/mail.php" method="get" target="_blank" class="newsletter-inner">
                                <input name="EMAIL" placeholder="Your email address" required="" type="email">
                                <button class="btn">Subscribe</button>
                            </form>
                        </div>
                        <!-- End Newsletter Inner -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Shop Newsletter -->
@endsection
@push('styles')
    <style>
        li.shipping {
            display: inline-flex;
            width: 100%;
            font-size: 14px;
        }

        li.shipping .input-group-icon {
            width: 100%;
            margin-left: 10px;
        }

        .input-group-icon .icon {
            position: absolute;
            left: 20px;
            top: 0;
            line-height: 40px;
            z-index: 3;
        }

        .text-bold {
            font-weight: bold !important;
        }


        .list li {
            margin-bottom: 0 !important;
        }

        .hide {
            display: none !important;
        }

        .list li:hover {
            background: #F7941D !important;
            color: white !important;
        }

        .custom-select.select2-container .select2-selection--single {
            padding: 0.625rem;

        }

        /* Gaya untuk select dengan atribut name */
        select[name="province"],
        select[name="district"],
        select[name="shipping"],
        select[name="city"] {
            display: block !important;
            appearance: none;
            outline: 0;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            box-shadow: none;
            padding: 0.375rem 0.75rem;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            width: 100% !important;
            height: calc(2em + 1rem + 4px) !important;
        }

        /* Gaya saat select dengan atribut name dalam keadaan fokus */
        select[name="province"]:focus,
        select[name="district"]:focus,
        select[name="shipping"]:focus,
        select[name="city"]:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Gaya saat select dengan atribut name saat hover */
        select[name="province"]:hover,
        select[name="district"]:hover,
        select[name="shipping"]:hover,
        select[name="city"]:hover {
            border-color: #adb5bd;
        }

        .nice-select {
            display: none !important;
        }


        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
@push('scripts')
    {{--	<script src="{{asset('frontend/js/nice-select/js/jquery.nice-select.min.js')}}"></script> --}}
    {{--	<script src="{{ asset('frontend/js/select2/js/select2.min.js') }}"></script> --}}
    <script>
        // $(document).ready(function() { $("select.select2").select2(); });
        // $('select.nice-select').niceSelect();
    </script>
    <script>
        function showMe(box) {
            var checkbox = document.getElementById('shipping').style.display;
            // alert(checkbox);
            var vis = 'none';
            if (checkbox == "none") {
                vis = 'block';
            }
            if (checkbox == "block") {
                vis = "none";
            }
            document.getElementById(box).style.display = vis;
        }
    </script>
    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
            $('.shipping select[name=shipping]').change(function () {
                let cost = parseFloat($(this).find('option:selected').data('price')) || 0;
                let subtotal = parseFloat($('.order_subtotal').data('price'));
                let coupon = parseFloat($('.coupon_price').data('price')) || 0;
                // alert(coupon);
                $('#order_total_price span').text('$' + (subtotal + cost - coupon).toFixed(2));
            });

        });
    </script>

    <script>
        $(document).ready(function () {
            $('#province').change(function () {
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
                    url: '{{ route('checkout.getCity') }}',
                    type: 'GET',
                    data: {
                        prov_id: prov_id
                    },
                    success: function (response) {
                        Swal.close();
                        var cities = response.data;

                        $('#city').empty();
                        $('#district').empty().append(
                            '<option value=""> - Select District - </option>');

                        cities.forEach(function (city) {
                            $('#city').append('<option value="' + city.city_name +
                                '" data-id="' + city.city_id + '"> ' + city
                                    .city_name + '</option>');
                        });

                        $('#city').trigger('change.select2');
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi masalah saat memuat data kota. Silakan coba lagi nanti.'
                        });
                        console.error(error);
                    }
                });
            });

            $('#city').change(function () {
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
                    url: '{{ route('checkout.getDistrict') }}',
                    type: 'GET',
                    data: {
                        city_id: city_id
                    },
                    success: function (response) {
                        Swal.close();
                        var districts = response.data;

                        $('#district').empty();

                        $('#district_id').val(districts[0].subdistrict_id);

                        districts.forEach(function (district) {
                            // console.log(district.subdistrict_name);
                            $('#district').append('<option value="' + district.type +
                                ' ' + district.subdistrict_name + '"' +
                                '" data-id="' + district.subdistrict_id + '">' +
                                district.subdistrict_name + '</option>');
                        });

                        $('#district').trigger(
                            'change'
                        ); // Trigger the change event to refresh Select2 (if applicable)

                        // Set district_id value based on the selected option's data-id
                        $('#district').change(function () {
                            var selectedDistrictId = $(this).find(':selected').data(
                                'id');
                            $('#district_id').val(selectedDistrictId);

                            Swal.fire({
                                title: 'Loading...',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: '{{ route('checkout.getCost') }}',
                                type: 'GET',
                                data: {
                                    destination: selectedDistrictId
                                },
                                success: function (response) {
                                    Swal.close();

                                    var shippingSelect = $('<select>', {
                                        name: 'shipping',
                                        class: 'form-control mt-2',
                                        required: true
                                    });

                                    shippingSelect.append($('<option>', {
                                        value: '',
                                        text: 'Select Shipping'
                                    }));

                                    var shippingCosts = response.cost;
                                    shippingCosts.forEach(function (cost) {
                                        var optionText = cost.name + ' - ' + cost.type + ': ' + cost.price;
                                        shippingSelect.append($('<option>', {
                                            value: cost.name + '-' + cost.type + '-' + cost.price,
                                            class: 'shippingOption',
                                            'data-price': cost.price,
                                            'data-name': cost.name,
                                            text: optionText
                                        }));
                                    });

                                    // Replace the existing shipping select with the newly built one
                                    $('#shipping').replaceWith(shippingSelect);

                                    // Trigger the change event to refresh Select2 (if applicable)
                                    shippingSelect.trigger('change');
                                }
                            });
                        });
                    },
                    error: function (xhr, status, error) {
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
@endpush
