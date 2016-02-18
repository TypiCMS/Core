@extends('core::admin.master')

@section('title', $title)

@section('main')

@include($module . '::admin._index')

@endsection
