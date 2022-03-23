<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @livewireStyles

</head>

<body>
    <div id="layout">
        @include('layouts.navbar')

        <section class="section" id="app">
            @if (session()->has('success'))
                <div class="success-box shadow-lg notification is-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="container">
                @yield('content')

                <portal-target name="portal-modal">
                </portal-target>
            </div>
        </section>

        <div id="footer" class="footer">
            <div class="content has-text-centered">
                &copy; {{ now()->format('Y') }} University of Glasgow, School of Engineering
            </div>
        </div>

    </div>

    @livewireScripts
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')

</body>

</html>
