@extends('core::admin.master')

@section('title', __('Verify'))
@section('bodyClass', 'auth-background')
@section('sidebar', '')
@section('mainClass', '')

@section('content')
    <div id="verify" class="container-verify auth">
        @include('users::_auth-header')

        <div class="auth-form">
            <div class="alert alert-success" role="alert">
                {{ __('Your email address has been verified.') }}
            </div>

            <div class="d-flex justify-content-center">
                <a class="btn btn-light" href="{{ homeUrl() }}">@lang('Go to our homepage')</a>
            </div>
        </div>
    </div>
@endsection
