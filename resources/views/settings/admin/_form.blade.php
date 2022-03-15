<div class="row">
    <div class="mb-3 col-sm-12">
        <button class="btn-primary btn" type="submit">{{ __('Save') }}</button>
    </div>
</div>

<label class="form-label">{{ __('Website title') }}</label>
@foreach ($locales as $lang)
    <div class="mb-3">
        <div class="input-group">
            <span class="input-group-text">{{ strtoupper($lang) }}</span>
            <input class="form-control" type="text" name="{{ $lang }}[website_title]" value="{{ $data->$lang->website_title ?? '' }}">
        </div>
    </div>
@endforeach

<label class="form-label">{{ __('Publish website') }}</label>

<div class="mb-3">
@foreach ($locales as $lang)
<div class="form-check form-check-inline">
    <input type="hidden" name="{{ $lang }}[status]" value="0">
    <input class="form-check-input" type="checkbox" name="{{ $lang }}[status]" id="{{ $lang }}[status]" value="1" @if (isset($data->$lang) and $data->$lang->status)checked @endif>
    <label class="form-check-label" for="{{ $lang }}[status]">{{ strtoupper($lang) }}</label>
</div>
@endforeach
</div>

<label class="form-label">{{ __('Website baseline') }}</label>
@foreach ($locales as $lang)
    <div class="mb-3">
        <div class="input-group">
            <span class="input-group-text">{{ strtoupper($lang) }}</span>
            <input class="form-control" type="text" name="{{ $lang }}[website_baseline]" value="{{ $data->$lang->website_baseline ?? '' }}">
        </div>
    </div>
@endforeach

<div class="fieldset-media fieldset-image">
    {!! BootForm::hidden('image') !!}
    @if (isset($data->image) and $data->image)
    <div class="fieldset-preview">
        <img class="img-fluid" src="{{ Storage::url('settings/'.$data->image) }}" alt="">
        <small class="text-danger delete-attachment" data-table="settings" data-id="" data-field="image">@lang('Delete')</small>
    </div>
    @endif
    <div class="fieldset-field">
        {!! BootForm::file(__('Logo'), 'image') !!}
    </div>
</div>

{!! BootForm::email(__('Webmaster Email'), 'webmaster_email') !!}
@if (!config('typicms.welcome_message_url'))
    {!! BootForm::textarea(__('Administration Welcome Message'), 'welcome_message') !!}
@endif
{!! BootForm::select(__('Administration Language'), 'admin_locale', array_combine($locales, $locales)) !!}
{!! BootForm::text(__('Twitter'), 'twitter_site')->placeholder('@') !!}
{!! BootForm::text(__('Facebook App ID'), 'facebook_app_id') !!}
{!! BootForm::hidden('lang_chooser')->value(0) !!}
@if (config('typicms.main_locale_in_url'))
    {!! BootForm::checkbox(__('Lang Chooser'), 'lang_chooser') !!}
@endif
{!! BootForm::hidden('auth_public')->value(0) !!}
{!! BootForm::checkbox(__('Authenticate to view website'), 'auth_public') !!}
{!! BootForm::hidden('register')->value(0) !!}
{!! BootForm::checkbox(__('Registration allowed'), 'register') !!}
