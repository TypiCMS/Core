@extends('core::public.master')

@section('title', 'Error 404 – ' . $websiteTitle)

@section('bodyClass', 'error-404')

@section('main')

    <article class="http-error-message">
        <h2>@lang('db.Sorry, this page was not found').</h2>
        <p>
            @lang('db.Go to our homepage?', ['a_open' => '<a href="/">', 'a_close' => '</a>'])
        </p>
    </article>

@endsection
