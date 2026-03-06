<div class="header">
    <x-core::title :$model :default="__('Profile')" />
    <div class="btn-toolbar">
        <button class="btn btn-sm btn-primary" type="submit">{{ __('Save') }}</button>
    </div>
</div>

<div class="content">
    <x-core::form-errors />

    <div class="row gx-3">
        <div class="col-sm-6">
            {!! BootForm::text(__('First name'), 'first_name')->required()->autocomplete('off') !!}
        </div>
        <div class="col-sm-6">
            {!! BootForm::text(__('Last name'), 'last_name')->required()->autocomplete('off') !!}
        </div>
    </div>

    <div class="row gx-3">
        <div class="col">
            {!! BootForm::email(__('Email'), 'email')->autocomplete('off')->required() !!}
        </div>
    </div>

    <user-passkeys url-base="/api/users/{{ $model->id }}/passkeys" new-passkey-name="{{ auth()->user()->first_name }}'s passkey" :create-button="true"></user-passkeys>
</div>
