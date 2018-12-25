@if(false)
<li class="nav-item">
    <a class="nav-link" href="{{ '/ar/'.\Request::route()->getName() }}">@if(App::isLocale('ar')) <span class="badge badge-info"> @endif العربية @if(App::isLocale('ar'))</span>@endif</a>
</li>
/* TODO: Arabic support */ 
@endif 
<li class="nav-item">
    <a class="nav-link" href="{{ '/fr/'.\Request::route()->getName() }}">@if(App::isLocale('fr')) <span class="badge badge-info"> @endif Français @if(App::isLocale('fr'))</span>@endif</a>
</li>
<span style="color: #CCCECF;margin-top: .45rem;font-weight: 200;">|</span> 
@if (Route::has('login'))
@auth
<li class="nav-item active">
    <a class="nav-link" href="{{ url('/home') }}">الرئيسية</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ Auth::user()->name }} <span class="caret"></span>
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <!--<div class="dropdown-divider"></div>-->
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    تسجيل الخروج
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</li>
@else
<li class="nav-item">
    <a class="nav-link" href="{{ route('login') }}">دخول</a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('register') }}">تسجيل </a>
</li>
@endauth
@endif