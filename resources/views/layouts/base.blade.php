<!doctype html>
<html lang="de" xmlns="http://www.w3.org/1999/html">
<head>
    <title>DIVI Intensivregister Data</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Roboto:100,300,400,500,700|Philosopher:400,400i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ mix('css/style.css') }}">
</head>

<body>
    <header id="header" class="header header-hide">
        <div class="container">

            <div id="logo" class="pull-left">
                <h1><a href="{{ route('home') }}" class="scrollto">Divi Intensivregister Daten</a></h1>
            </div>

            <nav id="nav-menu-container">
                <ul class="nav-menu">
                    <li @if(Route::currentRouteName() === 'home') class="menu-active" @endif>
                        <a href="{{ route('home') }}">Home</a>
                    </li>
                    <li @if(Route::currentRouteName() === 'data.load.clinics') class="menu-active" @endif>
                        <a href="{{ route('data.load.clinics') }}">Auslastung</a>
                    </li>
                    <!--<li>
                        <a href="#features">Fallzahlen</a>
                    </li>
                    <li><a href="#screenshots">Über uns</a></li>-->
                </ul>
            </nav>
        </div>
    </header>

    <section>
        @yield('content')
    </section>

    <footer class="footer">
        <div>
            <div class="copyrights">
                <div class="container">
                    <p>
                        &copy; Copyright {{ date('Y') }} Jan Hohner, Lisa Lengenfelder.<br>
                        <a href="{{ route('impressum') }}">Impressum</a>
                    </p>
                    <div class="credits">
                        <!--
                        All the links in the footer should remain intact.
                        You can delete the links only if you purchased the pro version.
                        Licensing information: https://bootstrapmade.com/license/
                        Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=eStartup
                      -->
                        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ mix('js/all.js') }}"></script>
</body>

</html>
