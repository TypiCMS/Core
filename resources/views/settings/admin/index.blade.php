@extends('core::admin.master')

@section('title', __('Settings'))

@section('content')
    {!! BootForm::open()->multipart() !!}
    {!! BootForm::bind($data) !!}

    <div class="header">
        <h1 class="header-title">@lang('Settings')</h1>
        <div class="btn-toolbar header-toolbar">
            <button class="btn btn-sm btn-primary me-2" type="submit">{{ __('Save') }}</button>
            @if (config('laravel-model-caching.enabled'))
                <a class="btn btn-sm btn-light me-2" href="{{ route('admin::clear-cache') }}">
                    {{ __('Clear cache') }}
                </a>
            @endif
        </div>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-lg-7">
                <label class="form-label">{{ __('Website title') }}</label>
                @foreach ($locales as $locale)
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">{{ strtoupper($locale) }}</span>
                            <input class="form-control" type="text" name="{{ $locale }}[website_title]" value="{{ $data->$locale->website_title ?? '' }}" />
                        </div>
                    </div>
                @endforeach

                <label class="form-label">{{ __('Publish website') }}</label>

                <div class="mb-3">
                    @foreach ($locales as $locale)
                        <div class="form-check form-check-inline">
                            <input type="hidden" name="{{ $locale }}[status]" value="0" />
                            <input class="form-check-input" type="checkbox" name="{{ $locale }}[status]" id="{{ $locale }}[status]" value="1" @if (isset($data->$locale) and $data->$locale->status) checked @endif />
                            <label class="form-check-label" for="{{ $locale }}[status]">{{ strtoupper($locale) }}</label>
                        </div>
                    @endforeach
                </div>

                <label class="form-label">{{ __('Website baseline') }}</label>
                @foreach ($locales as $locale)
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">{{ strtoupper($locale) }}</span>
                            <input class="form-control" type="text" name="{{ $locale }}[website_baseline]" value="{{ $data->$locale->website_baseline ?? '' }}" />
                        </div>
                    </div>
                @endforeach

                @if (!config('typicms.welcome_message_url'))
                    {!! BootForm::textarea(__('Administration Welcome Message'), 'welcome_message')->rows(3) !!}
                @endif

                {!! BootForm::hidden('auth_public')->value(0) !!}
                {!! BootForm::checkbox(__('Authenticate to view website'), 'auth_public') !!}
                {!! BootForm::hidden('register')->value(0) !!}
                {!! BootForm::checkbox(__('Registration allowed'), 'register') !!}
            </div>

            <div class="col-lg-5">
                <table class="table table-sm">
                    <tbody>
                    <tr>
                        <th class="text-nowrap">
                            <small>@lang('Environment')</small>
                        </th>
                        <td><small>{{ App::environment() }}</small></td>
                    </tr>
                    <tr>
                        <th class="text-nowrap">
                            <small>@lang('System locales')</small>
                        </th>
                        <td>
                            <div class="container-system-locales">
                                <small>
                                    <?php
                                    try {
                                        system('locale -a');
                                    } catch (Exception $e) {
                                        echo $e->getMessage();
                                    } ?>
                                </small>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-nowrap">
                            <small>@lang('Active locale')</small>
                        </th>
                        <td><small>{{ config('app.locale') }}</small></td>
                    </tr>
                    <tr>
                        <th class="text-nowrap">
                            <small>@lang('Cache')</small>
                        </th>
                        <td>
                            <small>{{ config('laravel-model-caching.enabled') ? __('Yes') : __('No') }}</small>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {!! BootForm::close() !!}
@endsection
