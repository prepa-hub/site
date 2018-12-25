<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="icons/favicon.ico">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <!-- Font Icon -->
    <link rel="stylesheet" href="{{ asset('fonts/material-icon/css/material-design-iconic-font.min.css') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/framework.css') }}" media="all" type="text/css" /> 
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}" media="all" type="text/css" /> 
    @yield('add2header')
</head>

<body class="login"> 
    <!-- Begin page content -->
    <main role="main">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="text-center">
            <span class="text-muted">© {{ date('Y') }}. Tous les droits sont réservés.</span>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}" media="all" type="text/css"></script>
    <script src="{{ asset('js/app.js') }}" media="all" type="text/css"></script>  
    @yield('add2footer')
</body>

</html>
