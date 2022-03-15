<input type="hidden" name="permissions[]" value="change locale">
<input type="hidden" name="permissions[]" value="update preferences">
<input type="hidden" name="permissions[]" value="clear cache">

<div class="mb-3">
    <div class="form-check">
        {!! Form::checkbox('permissions[]', 'see navbar')->id('permission-see-navbar')->addClass('form-check-input') !!}
        <label class="form-check-label" for="permission-see-navbar">@lang('See navbar')</label>
    </div>
    <div class="form-check">
        {!! Form::checkbox('permissions[]', 'see dashboard')->id('permission-see-dashboard')->addClass('form-check-input') !!}
        <label class="form-check-label" for="permission-see-dashboard">@lang('Access dashboard')</label>
    </div>
    <div class="form-check">
        {!! Form::checkbox('permissions[]', 'read settings')->id('permission-read-settings')->addClass('form-check-input') !!}
        <label class="form-check-label" for="permission-read-settings">@lang('See settings')</label>
    </div>
    <div class="form-check">
        {!! Form::checkbox('permissions[]', 'update settings')->id('permission-update-settings')->addClass('form-check-input') !!}
        <label class="form-check-label" for="permission-update-settings">@lang('Change settings')</label>
    </div>
    <div class="form-check">
        {!! Form::checkbox('permissions[]', 'see history')->id('permission-see-history')->addClass('form-check-input') !!}
        <label class="form-check-label" for="permission-see-history">@lang('See history')</label>
    </div>
    <div class="form-check">
        {!! Form::checkbox('permissions[]', 'clear history')->id('permission-clear-history')->addClass('form-check-input') !!}
        <label class="form-check-label" for="permission-clear-history">@lang('Empty history')</label>
    </div>
    <div class="form-check">
        {!! Form::checkbox('permissions[]', 'see unpublished items')->id('permission-see-unpublished-items')->addClass('form-check-input') !!}
        <label class="form-check-label" for="permission-see-unpublished-items">@lang('Preview unpublished items')</label>
    </div>
</div>

<div class="permissions-modules">
    <h2 class="permissions-modules-title">{{ __('Modules') }}</h2>
    <div class="permissions-modules-items">
        @foreach (TypiCMS::permissions() as $module => $permissions)
        <div class="permissions-modules-item mt-4 mb-4">
            <label class="permissions-modules-item-title">{{ $module }}</label>
            @foreach ($permissions as $permission => $label)
            <div class="permissions-modules-item-checkbox checkbox">
                <div class="form-check">
                    {!! Form::checkbox('permissions[]', $permission)->id('permission-'.Str::slug($permission))->addClass('form-check-input') !!}
                    <label class="form-check-label" for="permission-{{ Str::slug($permission) }}">{{ __($label) }}</label>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
