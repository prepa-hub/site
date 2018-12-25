@if (Route::has('login'))

@auth
<li class="nav-item active">
    <a class="nav-link" href="{{ url('/home') }}" style="
    background: rgba(0,0,0,0.18);
    text-shadow: 0 1px black;
">Accueil</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false" style="
    background: rgba(0,0,0,0.18);
">
        @if(Auth::user()->first_name && Auth::user()->last_name)
        {{ substr(Auth::user()->first_name,0,1).'. '.strtoupper(Auth::user()->last_name) }}
        @else
        {{ Auth::user()->name }}
        @endif
        <span class="caret"></span>
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">

        @if(Voyager::can('add_files'))
        <a class="dropdown-item" href="/{{locale()->current()}}/upload"><i class="fa fa-file-upload"></i> Publier un
            document</a>
        @endif
        <a class="dropdown-item" href="/{{locale()->current()}}/settings"><i class="fa fa-wrench"></i> Paramètres du
            compte</a>
        <div class="dropdown-divider"></div>
        @if(Voyager::can('browse_admin'))
        <a class="dropdown-item" href="/admin"><i class="fa fa-cogs"></i> Espace Admin</a>
        @endif
        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <i class="fa fa-power-off"></i> Déconnexion
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</li>

@else
<li class="nav-item">
    <a class="nav-link" style="background: rgba(0,0,0,0.18);" style="background: rgba(0,0,0,0.18);" href="{{ route('login') }}">S'identifier</a>
</li>

<li class="nav-item">
    <a class="nav-link" style="background: rgba(0,0,0,0.18);" href="{{ route('register') }}">S'inscrire</a>
</li>
@endauth

@endif
<span style="color: #CCCECF;padding-top: .45rem;font-weight: 200;background: rgba(0,0,0,0.18);">|</span>
<li class="nav-item">
    <a class="nav-link" style="background: rgba(0,0,0,0.18);" href="{{ '/fr/'.\Request::route()->getName() }}">@if(App::isLocale('fr'))
        <span class="badge badge-info">
            @endif Français @if(App::isLocale('fr'))</span>@endif</a>
</li>

@if(false)
<li class="nav-item">
    <a class="nav-link" style="background: rgba(0,0,0,0.18);" href="{{ '/ar/'.\Request::route()->getName() }}">@if(App::isLocale('ar'))
        <span class="badge badge-info">
            @endif العربية @if(App::isLocale('ar'))</span>@endif</a>
</li>
@php
/* TODO: Arabic support */ 
@endphp
@endif 
