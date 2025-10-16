@extends('core::admin.master')

@section('title', 'Error 429')

@section('bodyClass', 'error-429')

@section('content')
    <div class="main-content">
        <div class="header">
            <h1 class="header-title mb-0">@lang('Error :code', ['code' => '429'])</h1>
        </div>
        <div class="content">
            <p class="mb-0">
                @lang('Too Many Requests')
            </p>
        </div>
    </div>
@endsection
