@extends('core::admin.master')

@section('title', $model->present()->title)

@section('content')

    {!! BootForm::open()->put()->action(route('admin::update-role', $model->id))->multipart()->role('form') !!}
    {!! BootForm::bind($model) !!}
        @include('roles::admin._form')
    {!! BootForm::close() !!}

@endsection
