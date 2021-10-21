@extends('core::public.master')

@section('title', 'Error 429 – '.$websiteTitle)

@section('bodyClass', 'error-429')

@section('content')

<header class="page-header">
    <div class="page-header-container">
        <h1 class="page-title">@lang('Error') 429</h1>
    </div>
</header>

<div class="page-body">
    <div class="page-body-container">
        <p class="lead">@lang('Too Many Requests').</p>
        <p>
            {!! trans('Go to our homepage?', ['a_open' => '<a href="/">', 'a_close' => '</a>']) !!}
        </p>
    </div>
</div>

@endsection
