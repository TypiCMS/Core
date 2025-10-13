@extends('core::admin.master')

@section('title', 'Error 403')

@section('bodyClass', 'error-403')

@section('content')
    <div class="main-content">
        <div class="header">
            <h1 class="header-title mb-0">@lang('Error :code', 403)</h1>
        </div>
        <div class="content">
            <p class="mb-0">
                @lang('Sorry, you are not authorized to view this page.')
            </p>
        </div>
    </div>
@endsection
