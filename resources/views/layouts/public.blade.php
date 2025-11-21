<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'NSINC - Sistema de Información de Normatividad de Comunicación')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .bg-burgundy {
            background-color: #691c32;
        }
        .text-burgundy {
            color: #691c32;
        }
        .border-burgundy {
            border-color: #691c32;
        }
        .bg-nav-green {
            background-color: #0c231e;
        }
        .text-gold {
            color: #b8956a;
        }
    </style>
</head>
<body class="antialiased">
    <main class="px-4 pt-8 mx-auto">
        <x-layout.topbar />
        <x-layout.header />

        <div class="pt-8">
            @yield('content')
        </div>
    </main>

    <div class="pt-12"></div>
    <x-layout.footer />
</body>
</html>
