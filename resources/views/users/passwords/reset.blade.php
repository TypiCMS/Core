@extends('core::admin.master')

@section('title', __('New password'))
@section('bodyClass', 'auth-background')

@section('page-header')
@endsection
@section('sidebar')
@endsection
@section('mainClass')
@endsection

@section('content')

<div id="login" class="container-newpassword auth auth-sm">

    @include('users::_auth-header')

    {!! BootForm::open()->action(route(app()->getLocale().'::password.request'))->addClass('auth-form') !!}

        <h1 class="auth-title">{{ __('New password') }}</h1>

        @include('users::_status')

        {!! BootForm::email(__('Email'), 'email')->addClass('form-control-lg')->autofocus(true)->required()->autocomplete('username') !!}
        {!! BootForm::password(__('Password'), 'password')->addClass('form-control-lg')->required()->autocomplete('new-password') !!}
        {!! BootForm::password(__('Password confirmation'), 'password_confirmation')->addClass('form-control-lg')->required()->autocomplete('new-password') !!}
        {!! BootForm::hidden('token')->value($token) !!}

        <div class="mb-3 mt-3 d-grid">
            {!! BootForm::submit(__('Change Password'), 'btn-primary')->addClass('btn-lg') !!}
        </div>

    {!! BootForm::close() !!}

</div>

@endsection
