@extends('core::admin.master')

@section('title', __('Settings'))

@section('content')

<h1>@lang('Settings')</h1>

<div class="row">

    <div class="col-lg-6">

    {!! BootForm::open()->multipart() !!}
    {!! BootForm::bind($data) !!}

        @include('settings::admin._form')

    {!! BootForm::close() !!}

    </div>

    <div class="col-lg-6">
        @if (config('laravel-model-caching.enabled'))
        <div class="mb-5 pull-right">
            <a href="{{ route('admin::clear-cache') }}" class="btn btn-light">{{ __('Clear cache') }}</a>
        </div>
        @endif

        <table class="table table-sm table-striped">
            <tbody>
                <tr>
                    <td class="w-25">@lang('Environment')</td>
                    <td><b>{{ App::environment() }}</b></td>
                </tr>
                <tr>
                    <td>@lang('System locales')</td>
                    <td>
                        <div class="container-system-locales">
                            <b><?php try { system('locale -a'); } catch (Exception $e) { echo $e->getMessage(); } ?></b>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Active locale')</td>
                    <td><b>{{ config('app.locale') }}</b></td>
                </tr>
                <tr>
                    <td>@lang('Cache')</td>
                    <td><b>{{ config('laravel-model-caching.enabled') ? __('Yes') : __('No') }}</b></td>
                </tr>
            </tbody>
        </table>

    </div>

</div>

@endsection
