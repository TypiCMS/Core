@extends('core::public.master')

@section('title', 'Error 403 – ' . $websiteTitle)

@section('bodyClass', 'error-403')

@section('main')

    <article class="http-error-message">
        <h1>@lang('db.Error :code', ['code' => '403'])</h1>
        <p>
            @lang('db.Sorry, you are not authorized to view this page').<br>
            @lang('db.Go to our homepage?', ['a_open' => '<a href="/">', 'a_close' => '</a>'])
        </p>
    </article>

@stop
