<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PENSCEDULER - PENS Smart Academic Scheduler</title>
    <link rel="stylesheet" href="{{ asset('css/pensceduler.css') }}">
</head>
<body>
@yield('content')
@stack('scripts')
<script src="{{ asset('js/pensceduler.js') }}"></script>
</body>
</html>
