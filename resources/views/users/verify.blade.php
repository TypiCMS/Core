@extends('core::admin.master')

@section('title', __('Verify'))
@section('bodyClass', 'auth-background')
@section('sidebar', '')
@section('mainClass', '')

@section('content')
    <div id="verify" class="container-verify auth">
        @include('users::_auth-header')

        <div class="auth-form">
            <h1 class="auth-title">{{ __('Verify Your Email Address') }}</h1>

            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            {{ __('Before proceeding, please check your email for a verification link.') }}
            {{ __('If you did not receive the email') }},
            <a href="{{ route(app()->getLocale() . '::verification.resend') }}">
                {{ __('click here to request another') }}
            </a>
            .
        </div>
    </div>
@endsection
