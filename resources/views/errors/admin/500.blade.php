@extends('core::admin.master')

@section('title', 'Error 500')

@section('bodyClass', 'error-500')

@section('content')
    <div class="main-content">
        <div class="header">
            <h1 class="header-title mb-0">@lang('Error :code', ['code' => '500'])</h1>
        </div>
        <div class="content">
            <p class="mb-0">
                @lang('Sorry, a server error occurred.')
            </p>
        </div>
    </div>
@endsection
