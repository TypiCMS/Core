@extends('core::admin.master')

@section('title', $title)

@section('main')

@include($module . '::admin.index')

@stop
