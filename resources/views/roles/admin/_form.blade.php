{!! BootForm::hidden('id') !!}

<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Roles')])
    @include('core::admin._title', ['default' => __('New role')])
    @component('core::admin._buttons-form', ['model' => $model, 'langSwitcher' => false])
    @endcomponent
</div>

<div class="content">

    <div class="row gx-3">
        <div class="col-sm-6">
            {!! BootForm::text(__('Name'), 'name')->required() !!}
        </div>
    </div>

    <label class="form-label">@lang('Role permissions')</label>
    @include('core::admin._permissions-form')

</div>
