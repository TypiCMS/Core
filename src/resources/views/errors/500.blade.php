@extends('core::public.master')

@section('title', 'Error 500 – ' . $websiteTitle)

@section('bodyClass', 'error-500')

@section('langSwitcher') @stop

@section('main')

    <article class="http-error-message">
        <h1>@lang('db.Error :code', ['code' => '500'])</h1>
        <p>
            @lang('db.Sorry, a server error occurred').
        </p>
    </article>

@stop
