<input type="hidden" name="permissions[]" value="change-locale">
<input type="hidden" name="permissions[]" value="update-preferences">
<input type="hidden" name="permissions[]" value="clear-cache">

<div class="checkbox">
    <label>
        {!! Form::checkbox('permissions[]', 'see-navbar') !!} @lang('roles::global.See navbar')
    </label>
</div>
<div class="checkbox">
    <label>
        {!! Form::checkbox('permissions[]', 'see-dashboard') !!} @lang('roles::global.Access dashboard')
    </label>
</div>
<div class="checkbox">
    <label>
        {!! Form::checkbox('permissions[]', 'see-settings') !!} @lang('roles::global.See settings')
    </label>
</div>
<div class="checkbox">
    <label>
        {!! Form::checkbox('permissions[]', 'update-setting') !!} @lang('roles::global.Change settings')
    </label>
</div>
<div class="checkbox">
    <label>
        {!! Form::checkbox('permissions[]', 'see-history') !!} @lang('roles::global.See history')
    </label>
</div>
<div class="checkbox">
    <label>
        {!! Form::checkbox('permissions[]', 'clear-history') !!} @lang('roles::global.Empty history')
    </label>
</div>
<div class="permissions-modules">
    <h2 class="permissions-modules-title">{{ __('Modules') }}</h2>
    <div class="permissions-modules-items">
        @foreach (TypiCMS::permissions() as $module => $permissions)
        <div class="permissions-modules-item">
            <h3 class="permissions-modules-item-title">{{ __(ucfirst($module)) }}</h3>
            @foreach ($permissions as $permission => $label)
            <div class="permissions-modules-item-checkbox checkbox">
                <label>{!! Form::checkbox('permissions[]', $permission) !!} {{ __($label) }}</label>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
