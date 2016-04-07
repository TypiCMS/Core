<input type="hidden" name="permissions[]" value="index-history">
<input type="hidden" name="permissions[]" value="destroy-history">
<input type="hidden" name="permissions[]" value="change-locale">
<input type="hidden" name="permissions[]" value="update-preferences">

<div class="checkbox">
    <label>
        <input type="checkbox" name="permissions[]" value="dashboard" @if(in_array('dashboard', $permissions))checked="checked"@endif> Dashboard
    </label>
</div>
<div class="checkbox">
    <label>
        <input type="checkbox" name="permissions[]" value="index-settings" @if(in_array('index-settings', $permissions))checked="checked"@endif> Settings
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
                <td><input type="checkbox" name="permissions[]" value="index-{{ $module }}" @if(in_array('index-'.$module, $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="show-{{ str_singular($module) }}" @if(in_array('show-'.str_singular($module), $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="create-{{ str_singular($module) }}" @if(in_array('create-'.str_singular($module), $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="store-{{ str_singular($module) }}" @if(in_array('store-'.str_singular($module), $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="edit-{{ str_singular($module) }}" @if(in_array('edit-'.str_singular($module), $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="update-{{ str_singular($module) }}" @if(in_array('update-'.str_singular($module), $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="sort-{{ $module }}" @if(in_array('sort-'.$module, $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="destroy-{{ str_singular($module) }}" @if(in_array('destroy-'.str_singular($module), $permissions))checked="checked"@endif></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
