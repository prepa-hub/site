@extends('auth.layout')
@section('title')
Se connecter à {{ config('app.name', 'Laravel') }}
@endsection
@section('content')
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="img/auth.svg" alt="sing up image"></figure>
                        <a href="/register" class="signup-image-link">S'inscrire sur {{ config('app.name', 'Laravel') }}</a><!-- '-->
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">Se connecter</h2>
                        @include('auth.messages')
                        <form method="POST" class="register-form" id="login-form" action="{{ url('login') }}">
                            @csrf
                            <div class="form-group">
                                    <label for="email"><i class="zmdi zmdi-email"></i></label>
                                    <input type="email" name="email" id="email" placeholder="Email" required/>
                            </div>
                            <div class="form-group">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="Mot de pass"/>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="remember-me" id="remember-me" class="agree-term" />
                                <label for="remember-me" class="label-agree-term"><span><span></span></span>Remember me</label>
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="Log in"/>
                            </div>
                        </form>
                        @if(false)
                        <div class="social-login">
                            <span class="social-label">Or login with</span>
                            <ul class="socials">
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-facebook"></i></a></li>
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-twitter"></i></a></li>
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-google"></i></a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                
            </div>
        </section>
 
@endsection
