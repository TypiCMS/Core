@extends('core::admin.master')

@section('title', __('New taxonomy'))

@section('content')

    {!! BootForm::open()->action(route('admin::index-taxonomies'))->multipart()->role('form') !!}
        @include('taxonomies::admin._form')
    {!! BootForm::close() !!}

@endsection
