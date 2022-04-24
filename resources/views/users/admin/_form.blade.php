<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Users')])
    @include('core::admin._title', ['default' => __('New user')])
    @component('core::admin._buttons-form', ['model' => $model, 'langSwitcher' => false])
    @endcomponent
</div>

<div class="content">

    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}

    <div class="row gx-3">
        <div class="col-sm-6">
            {!! BootForm::text(__('First name'), 'first_name')->required() !!}
        </div>
        <div class="col-sm-6">
            {!! BootForm::text(__('Last name'), 'last_name')->required() !!}
        </div>
    </div>

    <div class="row gx-3">
        <div class="col">
            {!! BootForm::email(__('Email'), 'email') !!}
        </div>
        <div class="col">
            {!! BootForm::text(__('Phone'), 'phone') !!}
        </div>
    </div>

    <div class="row gx-3">
        <div class="col-sm-6">
            {!! BootForm::password(__('Password'), 'password') !!}
        </div>
        <div class="col-sm-6">
            {!! BootForm::password(__('Password confirmation'), 'password_confirmation') !!}
        </div>
    </div>

    <div class="row gx-3">
        <div class="col">
            {!! BootForm::text(__('Street'), 'street') !!}
        </div>
        <div class="col-md-2">
            {!! BootForm::text(__('Number'), 'number') !!}
        </div>
        <div class="col-md-2">
            {!! BootForm::text(__('Box'), 'box') !!}
        </div>
    </div>


    <div class="row gx-3">
        <div class="col">
            {!! BootForm::text(__('Postal code'), 'postal_code') !!}
        </div>
        <div class="col">
            {!! BootForm::text(__('City'), 'city') !!}
        </div>
        <div class="col">
            {!! BootForm::text(__('Country'), 'country') !!}
        </div>
    </div>

    <div class="row gx-3">
        <div class="col-6 col-lg-2">
            {!! BootForm::select(__('Language'), 'locale', ['' => '', 'en' => 'en', 'fr' => 'fr', 'nl' => 'nl', 'es' => 'es']) !!}
        </div>
    </div>

    <div class="mb-3">
    {!! BootForm::hidden('activated')->value(0) !!}
    {!! BootForm::checkbox(__('Activated'), 'activated') !!}
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Roles') }}</label>
        @if (auth()->user()->isSuperUser())
        {!! BootForm::hidden('superuser')->value(0) !!}
        {!! BootForm::checkbox(__('Superuser'), 'superuser') !!}
        @endif
        @if ($roles->count() > 0)
        @foreach ($roles as $role)
        <div class="form-check">
            {!! Form::checkbox('checked_roles[]', $role->id)->addClass('form-check-input')->id('role-'.$role->name) !!}
            <label class="form-check-label" for="{{ 'role-'.$role->name }}">{{ $role->name }}</label>
        </div>
        @endforeach
        @endif
    </div>

    <!-- Per user permissions -->
    {{--
    <label class="form-label">{{ __('User permissions') }}</label>
    @include('core::admin._permissions-form')
    --}}

</div>
