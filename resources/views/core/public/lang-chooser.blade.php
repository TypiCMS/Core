@extends('core::public.master')

@section('lang-switcher') @endsection
@section('header') @endsection
@section('primary-nav') @endsection
@section('footer') @endsection
@section('bodyClass') lang-chooser @endsection
@section('skip-links') @endsection

@section('content')

    <div class="page-header lang-chooser-header">
        <h1 class="lang-chooser-title">Choose language</h1>
    </div>

    <ul class="lang-chooser-list">
        @foreach ($locales as $locale)
            <li class="lang-chooser-list-item">
                <a class="lang-chooser-list-anchor" href="{{ url($homepage->uri($locale)) }}">{{ __('languages.'.$locale) }}</a>
            </li>
        @endforeach
    </ul>

@endsection
