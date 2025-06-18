@extends('core::admin.master')

@section('title', __('Login'))
@section('bodyClass', 'auth-background')
@section('sidebar', '')
@section('mainClass', '')

@section('content')
    <div id="login" class="container-login auth auth-sm">
        @include('users::_auth-header')

        @if(!request()->boolean('password'))
            <x-authenticate-passkey />
        @else
            {!! BootForm::open()->addClass('auth-form') !!}

            <h1 class="auth-title">{{ __('Login') }}</h1>

            @include('users::_status')

            {!! BootForm::email(__('Email'), 'email')->addClass('form-control-lg')->autofocus(true)->required()->autocomplete('username') !!}
        
            <div class="mb-3 d-grid">
                {!! BootForm::submit(__('Login'), 'btn-primary')->addClass('btn-lg') !!}
            </div>

            {!! BootForm::close() !!}
        @endif

        @if (config('typicms.register'))
            <p class="alert alert-warning alert-not-a-member">
                @lang('Not a member?')
                <a class="alert-link" href="{{ route(app()->getLocale() . '::register') }}">
                    @lang('Become a member')
                </a>
                @lang('and get access to all the content of our website.')
            </p>
        @endif

        <p class="auth-back-to-website">
            <a class="auth-back-to-website-link" href="{{ homeUrl() }}">
                <i class="bi bi-arrow-left me-1"></i>
                {{ __('Back to the website') }}
            </a>
        </p>
    </div>
@endsection
