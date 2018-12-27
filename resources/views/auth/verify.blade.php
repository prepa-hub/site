@extends('auth.layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <h4 style="padding: 2rem;">{{ __('Verify Your Email Address') }}</h4>
@if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif
            <p style="margin-bottom: 2rem;">
                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }}, <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>
            or <a href="/">go back to the homepage</a>.</p>

        </div>
    </div>
</div>
@endsection
