<input type="hidden" value="index-history" name="permissions[]">
<input type="hidden" value="destroy-history" name="permissions[]">

<div class="checkbox">
    <label>
        <input type="checkbox" value="1" @if(isset($permissions['dashboard']) && $permissions['dashboard'])checked="checked"@endif name="permissions[dashboard]"> Dashboard
    </label>
</div>
<div class="checkbox">
    <label>
        <input type="checkbox" value="1" @if(isset($permissions['index-settings']) && $permissions['index-settings'])checked="checked"@endif name="permissions[index-settings]"> Settings
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
                <td><input type="checkbox" name="permissions[]" value="show-{{ $module }}" @if(in_array('show-'.$module, $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="create-{{ $module }}" @if(in_array('create-'.$module, $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="store-{{ $module }}" @if(in_array('store-'.$module, $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="edit-{{ $module }}" @if(in_array('edit-'.$module, $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="update-{{ $module }}" @if(in_array('update-'.$module, $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="sort-{{ $module }}" @if(in_array('sort-'.$module, $permissions))checked="checked"@endif></td>
                <td><input type="checkbox" name="permissions[]" value="destroy-{{ $module }}" @if(in_array('destroy-'.$module, $permissions))checked="checked"@endif></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
