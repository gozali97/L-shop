<!DOCTYPE html>
<html>

<head>
    <title>Confirm Payment Order Rejected</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lobster&display=swap');

        :root {
            --primary: {{ $settings->theme['primary_color'] }};
            --secondary: {{ $settings->theme['secondary_color'] }};
            --heading: {{ $settings->theme['heading_color'] }};
            --text: {{ $settings->theme['text_color'] }};
            --primary-button: {{ $settings->theme['primary_button_color'] }};
            --secondary-button: {{ $settings->theme['secondary_button_color'] }};
        }

        * {
            color: var(--text);
        }

        a.link {
            color: var(--primary);
        }

        a.link:hover {
            color: var(--secondary);
        }

        .header-brand {
            height: 40px;
            line-height: 40px;
            background: var(--primary);
            color: var(--heading);
            width: 100%;
            text-align: center;
            font-family: 'Lobster', cursive;
        }

        .header-brand img {
            height: 30px;
            margin: 10px auto;
        }

        .color-primary {
            background-color: var(--primary);
        }

        .color-primary:hover {
            background-color: var(--secondary);
        }

        .color-primary-button {
            background-color: var(--primary-button);
        }

        .color-primary-button:hover {
            background-color: var(--secondary-button);
        }
    </style>
</head>

<body>
<div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
    <div
        class="relative bg-white px-6 pt-10 pb-8 shadow-xl ring-1 ring-gray-900/5 sm:mx-auto sm:max-w-lg sm:rounded-lg sm:px-10">
        <div class="w-full header-brand color-primary">
            <a href="{{ env('APP_URL') }}">
                @if($settings->brand_logo)
                    <img src="{{ $brandLogo }}" class="mt-2 rounded"  alt="{{ $settings->brand_name }}" height="100%">
                @else
                    {{ $settings->brand_name }}
                @endif
            </a>
        </div>
        <div class="mx-auto max-w-md">
            <div class="divide-y divide-gray-300/50">
                <div class="space-y-6 flex flex-col w-full py-8 text-base leading-7 text-gray-600">
                    <p class="float-right uppercase">Order ID: #{{ $order->order_number }}</p>
                    <h5 class="font-bold text-xl">Payment Request Rejected</h5>
                    @php
                        $reason = '';
                        if($note == 'min-value'){
                            $reason = 'Tender amount are not match';
                        }elseif ($note == 'not-received'){
                            $reason = 'Funds have not yet settle on our account';
                        }else{
                            $reason = 'Proof of Transfer is invalid/unclear';
                        }
                    @endphp
                        <p>Reason : {{$reason}}</p>
                        <div class="flex flex-row">
                            <a href="{{ route('user.order.show', $order->id ?? 1) }}" class="color-primary-button text-white px-2 rounded font-semibold">View Order</a>
                            <p class="ml-2">or</p>
                            <a href="{{ env('APP_URL') }}" class="ml-2 link">Visit our website</a>
                        </div>
                        <h5 class="font-semibold text-lg">Order Summary</h5>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        #
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Product
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        1
                                    </th>
                                    <td class="px-6 py-4">
                                        {{$order->first_name}}
                                    </td>
                                    <td class="px-6 py-4">
                                        <ul>
                                            @foreach($cart as $product)
                                                <li>{{$product->title}}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{$order->total_amount}}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{$order->payment_status}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div>
                            <h5>Customer Information</h5>
                            <div class="grid grid-cols-2">
                                <div>
                                    <p class="font-semibold">Shipping Address</p>
                                    <p>{{ $address->first_name }} {{ $address->last_name }}</p>
                                    <p>{{ $address->line_one }}</p>
                                    <p>{{ $address->city }}</p>
                                    <p>{{ $address->state }} {{ $address->postcode }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Billing Address</p>
                                    <p>{{ $address->first_name }} {{ $address->last_name }}</p>
                                    <p>{{ $address->address }}</p>
                                    <p>{{ $address->city }}</p>
                                    <p>{{ $address->state }} {{ $address->postcode }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 mt-2">
                                <div>
                                    <p class="font-semibold">Shipping</p>
                                    <p>{{ $address->shipping_option }}</p>
                                    <p>{{ $address->type }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Payment Method</p>
                                    <p>{{ ucwords(str_replace('-', ' ', $order->payment_method)) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full header-brand color-primary">
                            <a href="{{ env('APP_URL') }}">
                                @if($settings->brand_logo)
                                    <img src="{{ $brandLogo }}" class="mt-2 rounded" alt="{{ $settings->brand_name }}" height="100%">
                                @else
                                    {{ $settings->brand_name }}
                                @endif
                            </a>
                        </div>
                        <p class="text-center">Perfect for learning how the framework works, prototyping a new idea,
                            or creating a demo to share online.</p>
                        {{--                    <div class="flex w-full gap-2 text-xs justify-center">--}}
                        {{--                        <a href="#" class="text-green-800 font-semibold">Shop By Request</a>--}}
                        {{--                        <span class="text-green-800">|</span>--}}
                        {{--                        <a href="#" class="text-green-800 font-semibold">Consignment Program</a>--}}
                        {{--                        <span class="text-green-800">|</span>--}}
                        {{--                        <a href="#" class="text-green-800 font-semibold">Reseller Program</a>--}}
                        {{--                    </div>--}}
                        <div class="flex w-full justify-center justify-items-center gap-4">
                            <a target="_blank" href="{{ route('about-us') }}" class="text-white font-semibold color-primary-button px-2 rounded">About Us</a>
                            <a target="_blank" href="{{ env('APP_URL') }}" class="text-white font-semibold color-primary-button px-2 rounded">Our Store</a>
                        </div>
                        <div class="flex flex-col w-full items-center">
                            <p>Contact us on WhatsApp</p>
                            <a href="https://wa.me/62{{ $settings->socmed_wa }}"
                               class="font-semibold link">+62{{ $settings->socmed_wa }}</a>
                        </div>
                        <div>
                            <x-socmed.email-wrapper socmed_instagram="{{ $settings->socmed_instagram }}" socmed_facebook="{{ $settings->socmed_facebook }}" />
                        </div>
                </div>
                <div class="flex flex-col w-full items-center justify-center mt-4">
                    <p class="text-gray-600 mt-4 text-sm">
                        <span class="inline-flex items-center">
                            <x-socmed.copyright />
                            <i class="text-sm text-gray-600"> {{ date('Y') }} {{ $settings->brand_name }},
                                All Rights Reserved</i>
                        </span>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>

</body>

</html>
