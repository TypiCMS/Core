@extends('core::public.master')

@section('title', 'Error 419 – '.$websiteTitle)

@section('bodyClass', 'error-419')

@section('content')

<header class="page-header">
    <div class="page-header-container">
        <h1 class="page-title">@lang('Error') 419</h1>
    </div>
</header>

<div class="page-body">
    <div class="page-body-container">
        <p class="lead">@lang('Page Expired').</p>
        <p>
            {!! trans('Go to our homepage?', ['a_open' => '<a href="/">', 'a_close' => '</a>']) !!}
        </p>
    </div>
</div>

@endsection
