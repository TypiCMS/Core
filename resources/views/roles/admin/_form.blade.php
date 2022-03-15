{!! BootForm::hidden('id') !!}

@component('core::admin._buttons-form', ['model' => $model, 'langSwitcher' => false])
@endcomponent

<div class="row gx-3">
    <div class="col-sm-6">
        {!! BootForm::text(__('Name'), 'name')->required() !!}
    </div>
</div>

<label class="form-label">@lang('Role permissions')</label>
@include('core::admin._permissions-form')
