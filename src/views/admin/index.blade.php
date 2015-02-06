@extends('core::admin.master')

@section('main')

@include(Request::segment(2) . '::admin.index')

@stop
