@extends('core::admin.master')

@section('title', 'Error 419')

@section('bodyClass', 'error-419')

@section('content')
    <div class="main-content">
        <div class="header">
            <h1 class="header-title mb-0">@lang('Error :code', ['code' => '419'])</h1>
        </div>
        <div class="content">
            <p class="mb-0">
                @lang('Page Expired')
            </p>
        </div>
    </div>
@endsection
