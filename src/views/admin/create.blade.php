@extends('core::admin.master')

@section('title', trans($model->getTable() . '::global.New'))

@section('main')

    <h1>
        @include('core::admin._button-back', ['table' => $model->getTable()])
        @lang($model->getTable() . '::global.New')
    </h1>

    {!! BootForm::open()->action(route('admin.' . $model->getTable() . '.index'))->multipart()->role('form') !!}
    {!! BootForm::token() !!}
        @include($model->getTable() . '::admin._form')
    {!! BootForm::close() !!}

@stop
