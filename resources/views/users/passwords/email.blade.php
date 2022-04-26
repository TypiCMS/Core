@extends('core::admin.master')

@section('title', __('Reset Password'))
@section('bodyClass', 'auth-background')

@section('page-header')
@endsection
@section('sidebar')
@endsection
@section('mainClass')
@endsection

@section('content')

<div id="reset" class="container-reset auth auth-sm">

    @include('users::_auth-header')

    {!! BootForm::open()->action(route(app()->getLocale().'::password.email'))->addClass('auth-form') !!}

        <h1 class="auth-title">{{ __('Reset Password') }}</h1>

        @include('users::_status')

        {!! BootForm::email(__('Email'), 'email')->addClass('form-control form-control-lg')->autofocus(true)->required()->autocomplete('username') !!}

        <div class="mb-3 mt-3 d-grid">
            {!! BootForm::submit(__('Send Password Reset Link'), 'btn-primary')->addClass('btn-lg') !!}
        </div>

    {!! BootForm::close() !!}

</div>

@endsection
