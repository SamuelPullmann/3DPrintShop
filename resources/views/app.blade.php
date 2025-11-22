<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', '3D Print Shop') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-100 text-gray-900">
        <div id="app" class="min-h-screen flex items-center justify-center">
            <h1 class="text-3xl font-bold">Welcome to 3D Print Shop</h1>
        </div>
    </body>
</html>

