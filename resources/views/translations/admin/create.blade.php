@extends('core::admin.master')

@section('title', __('New translation'))

@section('content')

    {!! BootForm::open()->action(route('admin::index-translations'))->multipart()->role('form') !!}
        @include('translations::admin._form')
    {!! BootForm::close() !!}

@endsection
