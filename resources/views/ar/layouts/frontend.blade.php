<!doctype html>
<html lang="{{locale()->current()}}">

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
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/framework-rtl.css') }}" media="all" type="text/css" /> 
    <link rel="stylesheet" href="{{ asset('css/app-rtl.css') }}" media="all" type="text/css" /> 
    @yield('add2header')
    <style>
        .paral{
            background-image: url("{{ asset('img/bg2.jpg') }}") 
        }
    </style>
</head>

<body>
        <header class="paral">
            <nav class="navbar navbar-expand-md navbar-dark bg-transparent navbar-static-top">
            <div class="container">
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav">
                       @include('ar.menu')
                    </ul>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <a class="navbar-brand mr-auto" href="#">اللوغو</a>
            </div>
        </nav>
        
    <div class="mx-auto text-center container">
            <h1 class="mt-5">مرحبا</h1>
            <hr width="100">
            <p class="lead">مرحبا بك في ملفات، هنا كل الملفات المرفوعة في موقعنا، اختر مستواك لتجد الملفات التي ستحتاجها</p>
            <br>
            
    <div class="row">
            <div class="col-4">
                <button type="button" class="btn btn-lg btn-outline-light btn-levels">الثانوي</button>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-lg btn-outline-light btn-levels">الإعدادي</button>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-lg btn-outline-light btn-levels">الإبتدائي</button>
            </div>
        </div>
    </div>
    </header>

    <!-- Begin page content -->
    <main role="main" class="container">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <span class="text-muted">{{ date('Y') }} © - جميع الحقوق محفوظة</span>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.rtlcss.com/bootstrap/v4.0.0/js/bootstrap.min.js" integrity="sha384-54+cucJ4QbVb99v8dcttx/0JRx4FHMmhOWi4W+xrXpKcsKQodCBwAvu3xxkZAwsH" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}" media="all" type="text/css"></script>  
    @yield('add2footer')
</body>

</html>
