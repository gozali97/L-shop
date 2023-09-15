<!-- Meta Tag -->
@yield('meta')
<!-- Title Tag  -->
<title>@yield('title')</title>
<!-- Favicon -->
<link rel="icon" type="image/png" href="images/favicon.png">
<!-- Web Font -->
<link
    href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap"
    rel="stylesheet">

<!-- StyleSheet -->
<link rel="manifest" href="/manifest.json">
<!-- Bootstrap -->
<link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.css') }}">
<!-- Magnific Popup -->
<link rel="stylesheet" href="{{ asset('frontend/css/magnific-popup.min.css') }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset('frontend/css/font-awesome.css') }}">
<!-- Fancybox -->
<link rel="stylesheet" href="{{ asset('frontend/css/jquery.fancybox.min.css') }}">
<!-- Themify Icons -->
<link rel="stylesheet" href="{{ asset('frontend/css/themify-icons.css') }}">
<!-- Nice Select CSS -->
<link rel="stylesheet" href="{{ asset('frontend/css/niceselect.css') }}">
<!-- Animate CSS -->
<link rel="stylesheet" href="{{ asset('frontend/css/animate.css') }}">
<!-- Flex Slider CSS -->
<link rel="stylesheet" href="{{ asset('frontend/css/flex-slider.min.css') }}">
<!-- Owl Carousel -->
<link rel="stylesheet" href="{{ asset('frontend/css/owl-carousel.css') }}">
<!-- Slicknav -->
<link rel="stylesheet" href="{{ asset('frontend/css/slicknav.min.css') }}">
<!-- Jquery Ui -->
<link rel="stylesheet" href="{{ asset('frontend/css/jquery-ui.css') }}">

<!--  StyleSheet -->
<link rel="stylesheet" href="{{ asset('frontend/css/reset.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/css/responsive.css') }}">
<link href="{{ url('/vendor/select2/css/select2.min.css') }}" rel="stylesheet" />

<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
<script type='text/javascript'
    src='//platform-api.sharethis.com/js/sharethis.js#property=5f2e5abf393162001291e431&product=inline-share-buttons'
    async='async'></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.all.min.js"></script>
<script src="{{ url('/vendor/select2/js/select2.min.js') }}"></script>
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous"> --}}
<style>
    /* Multilevel dropdown */
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu>a:after {
        content: "\f0da";
        float: right;
        border: none;
        font-family: 'FontAwesome';
    }

    .dropdown-submenu>.dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: 0px;
        margin-left: 0px;
    }

    .btn {
        border-radius: 5% !important;
    }

    .header-inner {
        background-color: #FFC6AC !important;
    }

    .btn-head:hover {
        background-color: #DAC0A3 !important;
    }

    /*
</style>
@stack('styles')
