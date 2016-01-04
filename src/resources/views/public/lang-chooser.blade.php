@extends('core::public.master')

@section('langSwitcher') @stop
@section('mainHeader') @stop
@section('mainNav') @stop
@section('mainFooter') @stop
@section('bodyClass') lang-chooser @stop

@section('main')

    <div class="page-header lang-chooser-header">
        <h1 class="lang-chooser-title">Choose language</h1>
    </div>

    <ul class="lang-chooser-list">
        @foreach ($locales as $locale)
            <li class="lang-chooser-list-item">
                <a class="lang-chooser-list-anchor" href="{{ url($homepage->uri($locale)) }}">{{ trans('db.languages.'.$locale) }}</a>
            </li>
        @endforeach
    </ul>

@stop
