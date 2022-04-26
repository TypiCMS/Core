@extends('core::admin.master')

@section('title', __('Register'))
@section('bodyClass', 'auth-background')

@section('page-header')
@endsection
@section('sidebar')
@endsection
@section('mainClass')
@endsection

@section('content')

<div id="register" class="container-register auth">

    @include('users::_auth-header')

    {!! BootForm::open()->addClass('auth-form') !!}

        <h1 class="auth-title">{{ __('Register') }}</h1>

        @include('users::_status')

        {!! Honeypot::generate('my_name', 'my_time') !!}

        {!! BootForm::email(__('Email'), 'email')->addClass('form-control-lg')->required()->autocomplete('username') !!}
        <div class="row gx-3">
            <div class="col-sm-6">
                {!! BootForm::text(__('First name'), 'first_name')->addClass('form-control-lg')->required() !!}
            </div>
            <div class="col-sm-6">
                {!! BootForm::text(__('Last name'), 'last_name')->addClass('form-control-lg')->required() !!}
            </div>
        </div>
        <div class="row gx-3">
            <div class="col-sm-6">
                {!! BootForm::password(__('Password'), 'password')->addClass('form-control-lg')->required()->autocomplete('new-password') !!}
            </div>
            <div class="col-sm-6">
                {!! BootForm::password(__('Password confirmation'), 'password_confirmation')->addClass('form-control-lg')->required()->autocomplete('new-password') !!}
            </div>
        </div>

        <div class="mb-3 mt-3 d-grid">
            {!! BootForm::submit(__('Register'), 'btn-primary')->addClass('btn-lg') !!}
        </div>

    {!! BootForm::close() !!}

</div>

@endsection
