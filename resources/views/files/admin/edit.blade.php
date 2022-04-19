@extends('core::admin.master')

@section('title', $model->present()->title)

@section('content')

    {!! BootForm::open()->put()->action(route('admin::update-file', $model->id))->multipart()->role('form') !!}
    {!! BootForm::bind($model) !!}
        @include('files::admin._form')
    {!! BootForm::close() !!}

@endsection
