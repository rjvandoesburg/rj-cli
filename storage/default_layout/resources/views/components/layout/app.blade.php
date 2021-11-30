<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <script defer src="{{ mix('js/app.js') }}"></script>
</head>

<body class="font-sans antialiased">
{{ $slot }}
</body>
</html>
