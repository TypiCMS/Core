@extends('core::admin.master')

@section('title', __('New menu'))

@section('content')

    {!! BootForm::open()->action(route('admin::index-menus'))->multipart()->role('form') !!}
        @include('menus::admin._form')
    {!! BootForm::close() !!}

@endsection
