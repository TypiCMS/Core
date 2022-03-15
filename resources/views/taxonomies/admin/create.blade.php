@extends('core::admin.master')

@section('title', __('New taxonomy'))

@section('content')

    <div class="header">
        @include('core::admin._button-back', ['module' => 'taxonomies'])
        <h1 class="header-title">@lang('New taxonomy')</h1>
    </div>

    {!! BootForm::open()->action(route('admin::index-taxonomies'))->multipart()->role('form') !!}
        @include('taxonomies::admin._form')
    {!! BootForm::close() !!}

@endsection
