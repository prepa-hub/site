<!doctype html>
<html lang="{{locale()->current()}}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="icon" href="icons/favicon.ico">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/framework.css') }}" media="all" type="text/css" /> 
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" media="all" type="text/css" /> 
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/css/bootstrap-select.min.css">

    @yield('add2header')
    <style>
        .paral{
            background-image: url("{{ Voyager::image(setting('site.header'))  }}") 
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark navbar-static-top">
            <div class="container">
                <a class="navbar-brand mr-auto" href="/"><img height="35px" src="{{ Voyager::image(setting('site.logo')) }}" height="60px" alt="{{ setting('site.title') }} - {{ setting('site.description') }}"/></a>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
                        @include('menu')
                    </ul>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
        @if(false)
        <div>
            <div class="mx-auto text-center container">
                <h1>{{ setting('site.header_h1') }}</h1>
                <hr width="100">
                <p class="lead">{{ setting('site.header_p') }}</p>
                <br>
                {{--  <div class="row">
                        <div class="col-4">
                            <a href="/"  onClick="submitSearch(event,'primaire')" class="btn btn-lg btn-outline-light btn-levels">Primaire</a>
                        </div>
                        <div class="col-4"> 
                            <a href="/" onClick="submitSearch(event,'college')" class="btn btn-lg btn-outline-light btn-levels" >College</a>
                        </div>
                        <div class="col-4">
                            <a href="/"  onClick="submitSearch(event,'lycée')" class="btn btn-lg btn-outline-light btn-levels">Lycée</a>
                        </div>
                    </div>  --}}
            </div>
        </div>
        @endif
    </header>

    <!-- Begin page content -->
    <main role="main">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <span class="text-muted">© {{ date('Y') }}. Tous les droits sont réservés.</span>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.3/js/i18n/defaults-fr_FR.min.js"></script>
    <script src="{{ asset('js/app.js') }}" media="all"></script>  
    <script src="{{ asset('js/app.js') }}" media="all"></script>  
    @yield('add2footer')
    <script>
        function submitSearch(evant,keyword){
            event.preventDefault();
            $('input[name=q]').val(keyword);
            $('.search_submit').click();
        }
    </script>
</body>

</html>
