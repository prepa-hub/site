@extends('layouts.layout')
@section('title','Paramètres du compte')
@section('add2header')
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
@endsection

@section('content')

<div class="container">
    <div class="row" style="height:2rem;"></div>
    <div class="row">
        <div class="col-8">
            <h1>Paramètres du compte</h1>
            <form method="POST" action="/updateProfile">
                <br>
                @csrf
                @include('messages')
                <div class="row form-group">
                    <div class="col">
                        <label for="">Prénom</label>
                        <input type="text" class="form-control" name="first_name" placeholder="Prénom" value="{{ Auth::user()->first_name }}">
                    </div>
                    <div class="col">
                        <label for="">Nom</label>
                        <input type="text" class="form-control" name="last_name" placeholder="Nom" value="{{ Auth::user()->last_name }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Adresse électronique</label>
                    <input type="text" class="form-control" name="email" placeholder="Email" value="{{ Auth::user()->email }}">
                </div>
                @if(Auth::user()->role_id == 2)
                @php 
                 /* TODO replace this | Professor only  */
                @endphp
                 <div class="form-group">
                    <div class="form-group">
                        <label for="subject_id">Matière</label>
                        <select class="selectpicker form-control show-tick" id="subject_id" name="subject_id" data-live-search="true" title="Sélectionner...">
                        @foreach ($subjects as $s)
                            @if($s->title)
                                <option value="{{ $s->id }}" data-tokens="{{ $s->abrev }}" @if(Auth::user()->subject_id == $s->id) selected @endif>{{ $s->title }}</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                </div>
                @endif
                    <div class="form-group">
                        <label for="">Changer mot de passe</label>
                        <input type="password" class="form-control" name="password" placeholder="Laisser vide pour garder l'ancien Mot de passe">
                    </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> Modifier mes coordonnées</button>
            </form>
        </div>
        <div class="col-4">
            <div class="row" style="height:6rem;"></div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <img class="card-img-top" width="18px" src="{{ Voyager::image(Auth::user()->avatar) }}" alt="Profile image">
                        <div class="card-footer">
                            <form method="POST" enctype="multipart/form-data" action="/uploadImage">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image">
                                        <label class="custom-file-label" for="">Choisir une image</label>
                                    </div>
                                    <input type="hidden" value="{{ csrf_token() }}" name="_token">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-secondary" type="button" id="">Remplacer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="height:2rem;"></div>
</div>
</div>
@endsection

@section('add2footer')
<script>
    // To style only selects with the selectpicker class
    $('.selectpicker').selectpicker();
</script>
@endsection
