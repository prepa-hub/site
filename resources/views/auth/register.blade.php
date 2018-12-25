@extends('auth.layout')

@section('title')
S'inscrire à {{ config('app.name', 'Laravel') }}
@endsection
@section('content') 

        <section class="signup">
                <div class="container">
                    <div class="signup-content">
                        <div class="signup-form">
                            <h2 class="form-title">S'inscrire</h2>
                            
                        @include('auth.messages')
                            <form method="POST" class="register-form" id="register-form" action="{{ url('register') }}">
                                    @csrf
                                <div class="form-group">
                                    <label for="username"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                    <input type="text" name="name" id="name" placeholder="Nom d'utilisateur" required/>
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="email"><i class="zmdi zmdi-email"></i></label>
                                    <input type="email" name="email" id="email" placeholder="Email" required/>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                                    <input type="password" name="password" id="password" placeholder="Mot de Pass" required/>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation"><i class="zmdi zmdi-lock-outline"></i></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmation de Mot de Pass" required/>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" required/>
                                    <label for="agree-term" class="label-agree-term"><span><span></span></span>En cliquant sur Inscription, vous acceptez nos <a href="#" class="term-service">Conditions générales</a></label>
                                </div>
                                <div class="form-group form-button">
                                    <input type="submit" name="signup" id="signup" class="form-submit" value="Inscription"/>
                                </div>
                            </form>
                        </div>
                        <div class="signup-image">
                            <figure><img src="img/register.svg" alt="sing up image"></figure>
                            <a href="/login" class="signup-image-link">Déja membre ? Se connecter à {{ config('app.name', 'Laravel') }}</a>
                        </div>
                    </div>
                </div>
            </section>
@endsection
