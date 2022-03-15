@extends('core::admin.master')

@section('title', __('Login'))
@section('bodyClass', 'auth-background')

@section('page-header')
@endsection
@section('sidebar')
@endsection
@section('mainClass')
@endsection
@section('errors')
@endsection

@section('content')

<div id="login" class="container-login auth-container auth-container-sm">

    @include('users::_logo')

    {!! BootForm::open()->addClass('auth-container-form') !!}

        <h1 class="auth-container-title">{{ __('Login') }}</h1>

        @include('users::_status')

        {!! BootForm::email(__('Email'), 'email')->addClass('form-control-lg')->autofocus(true)->required()->autocomplete('username') !!}
        {!! BootForm::password(__('Password'), 'password')->addClass('form-control-lg')->required()->autocomplete('current-password') !!}

        <div class="mb-3">
            {!! BootForm::checkbox(__('Remember Me'), 'remember') !!}
        </div>

        <div class="mb-3 d-grid">
            {!! BootForm::submit(__('Login'), 'btn-primary')->addClass('btn-lg') !!}
        </div>

        <a class="form-text mt-0 d-block" href="{{ route(app()->getLocale().'::password.request') }}">{{ __('Forgot Your Password?') }}</a>

    {!! BootForm::close() !!}

    @if (config('typicms.register'))
    <p class="alert alert-warning alert-not-a-member">
        @lang('Not a member?') <a class="alert-link" href="{{ route(app()->getLocale().'::register') }}">@lang('Become a member')</a> @lang('and get access to all the content of our website.')
    </p>
    @endif

    <p class="auth-container-back-to-website">
        <a class="auth-container-back-to-website-link" href="{{ TypiCMS::homeUrl() }}">
            <svg class="me-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>{{ __('Back to the website') }}
        </a>
    </p>

</div>

@endsection
