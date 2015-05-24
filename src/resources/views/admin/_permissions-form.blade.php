<input type="hidden" value="1" name="permissions[user.updatepreferences]">
<input type="hidden" value="1" name="permissions[history]">

<div class="checkbox">
    <label>
        <input type="checkbox" value="1" @if(isset($permissions['dashboard']) && $permissions['dashboard'])checked="checked"@endif name="permissions[dashboard]"> Dashboard
    </label>
</div>
<div class="checkbox">
    <label>
        <input type="checkbox" value="1" @if(isset($permissions['settings.index']) && $permissions['settings.index'])checked="checked"@endif name="permissions[settings.index]"> Settings
    </label>
</div>
<div class="table-responsive">
    <table class="table table-condensed table-permissions table-checkboxes">
        <thead>
            <tr>
                <th></th>
                <th>@lang('global.Index')</th>
                <th>@lang('global.View')</th>
                <th>@lang('global.Create')</th>
                <th>@lang('global.Store')</th>
                <th>@lang('global.Edit')</th>
                <th>@lang('global.Update')</th>
                <th>@lang('global.Sort')</th>
                <th>@lang('global.Delete')</th>
            </tr>
        </thead>
        <tbody>
        @foreach (TypiCMS::modules() as $module => $properties)
            <tr>
                <td>@lang($module.'::global.name')</td>
                <td><input type="checkbox" value="1" @if(isset($permissions[$module.'.index']) && $permissions[$module.'.index'])checked="checked"@endif name="permissions[{{ $module }}.index]"></td>
                <td><input type="checkbox" value="1" @if(isset($permissions[$module.'.show']) && $permissions[$module.'.show'])checked="checked"@endif name="permissions[{{ $module }}.show]"></td>
                <td><input type="checkbox" value="1" @if(isset($permissions[$module.'.create']) && $permissions[$module.'.create'])checked="checked"@endif name="permissions[{{ $module }}.create]"></td>
                <td><input type="checkbox" value="1" @if(isset($permissions[$module.'.store']) && $permissions[$module.'.store'])checked="checked"@endif name="permissions[{{ $module }}.store]"></td>
                <td><input type="checkbox" value="1" @if(isset($permissions[$module.'.edit']) && $permissions[$module.'.edit'])checked="checked"@endif name="permissions[{{ $module }}.edit]"></td>
                <td><input type="checkbox" value="1" @if(isset($permissions[$module.'.update']) && $permissions[$module.'.update'])checked="checked"@endif name="permissions[{{ $module }}.update]"></td>
                <td><input type="checkbox" value="1" @if(isset($permissions[$module.'.sort']) && $permissions[$module.'.sort'])checked="checked"@endif name="permissions[{{ $module }}.sort]"></td>
                <td><input type="checkbox" value="1" @if(isset($permissions[$module.'.destroy']) && $permissions[$module.'.destroy'])checked="checked"@endif name="permissions[{{ $module }}.destroy]"></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
