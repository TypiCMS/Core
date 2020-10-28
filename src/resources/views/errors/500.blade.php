@extends('core::public.master')

@section('title', 'Error 500 – '.$websiteTitle)

@section('bodyClass', 'error-500')
@section('lang-switcher') @endsection
@section('content')

<header class="page-header">
    <div class="page-header-container">
        <h1 class="page-title">@lang('Server Error')</h1>
    </div>
</header>

<div class="page-body">
    <div class="page-body-container">
        <p class="lead">@lang('Sorry, a server error occurred').</p>
        <p>
            {!! trans('Error :code', ['code' => '500']) !!}.
        </p>
    </div>
</div>

@endsection
