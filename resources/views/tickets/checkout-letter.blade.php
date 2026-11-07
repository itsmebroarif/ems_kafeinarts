<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Surat Checkout - {{ $event->name }}</title>
    @include('tickets.checkout-letter-styles')
</head>

<body>
    @include('tickets.checkout-letter-sheet')
</body>

</html>
