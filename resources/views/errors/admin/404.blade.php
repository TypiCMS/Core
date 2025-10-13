@extends('core::admin.master')

@section('title', 'Error 404')

@section('bodyClass', 'error-404')

@section('content')
    <div class="main-content">
        <div class="header">
            <h1 class="header-title mb-0">@lang('Error :code', 404)</h1>
        </div>
        <div class="content">
            <p class="mb-0">
                @lang('Sorry, this page was not found.')
            </p>
        </div>
    </div>
@endsection
