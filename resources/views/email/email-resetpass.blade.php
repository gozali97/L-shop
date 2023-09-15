<!DOCTYPE html>
<html>

<head>
    <title>Reset Password Confirmation</title>
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
                    <img src="{{ Helper::getBrandLogoUrl()  }}" alt="{{ $settings->brand_name }}" height="100%">
                @else
                    {{ $settings->brand_name }}
                @endif
            </a>
        </div>
        <div class="mx-auto max-w-md">
            <div class="divide-y divide-gray-300/50">
                <div class="space-y-6 py-8 text-base leading-5 text-gray-600">
                    <h3 class="font-bold text-xl mb-2">Hello {{$name}}!</h3>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500">
                            You are receiving this email because we received a password reset request for your account.
                        </p>
                    </div>
                    <div class="mb-6 flex items-center justify-center">
                        <a href="{{ route('password.reset', ['token' => $token]) }}"
                           class="color-primary-button px-2 rounded text-white text-sm py-1.5 mb-4">
                            Reset Password
                        </a>
                    </div>
                    <div class="w-full header-brand color-primary">
                        <a href="{{ env('APP_URL') }}">
                            @if($settings->brand_logo)
                                <img src="{{ Helper::getBrandLogoUrl() }}" alt="{{ $settings->brand_name }}" height="100%">
                            @else
                                {{ $settings->brand_name }}
                            @endif
                        </a>
                    </div>
                    <p class="text-center">Perfect for learning how the framework works, prototyping a new idea,
                        or creating a demo to share online.</p>
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
