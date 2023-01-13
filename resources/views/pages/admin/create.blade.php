@extends('core::admin.master')

@section('title', __('New page'))

@section('content')

    {!! BootForm::open()->action(route('admin::index-pages'))->multipart()->role('form') !!}
        @include('pages::admin._form')
    {!! BootForm::close() !!}

@endsection
