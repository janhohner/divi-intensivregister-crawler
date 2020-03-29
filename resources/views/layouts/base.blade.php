<!doctype html>
<html lang="de" xmlns="http://www.w3.org/1999/html">
<head>
    <title>DIVI Intensivregister Data</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ mix('css/style.css') }}">
</head>

<body>
    @yield('content')

    <script src="{{ mix('js/all.js') }}"></script>
</body>

</html>
