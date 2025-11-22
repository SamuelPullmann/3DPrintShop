<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', '3DPrintShop')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@include('partials.nav')

<main class="container" style="padding:2rem 0;">
    @yield('content')
</main>

<footer class="container" style="padding:1rem 0;border-top:1px solid #eaeaea;">
    <p>&copy; {{ date('Y') }} 3DPrintShop. All rights reserved.</p>
</footer>
</body>
</html>
