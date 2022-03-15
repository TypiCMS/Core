@extends('core::admin.master')

@section('title', __('Verify'))
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

<div id="verify" class="container-verify auth-container">

    @include('users::_logo')

    <div class="auth-container-form">

            <div class="alert alert-success" role="alert">
                {{ __('Your email address has been verified.') }}
            </div>

            <div class="d-flex justify-content-center">
                <a class="btn btn-secondary" href="{{ TypiCMS::homeUrl() }}">@lang('Go to our homepage')</a>
            </div>

        </div>

    </div>

</div>

@endsection
