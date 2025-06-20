@extends('core::admin.master')

@section('title', __('Login'))
@section('bodyClass', 'auth-background')
@section('sidebar', '')
@section('mainClass', '')
@section('navBar', '')

@section('content')
    <div id="login" class="container-login auth auth-sm">
        <x-users::auth-header />

        {!! BootForm::open()->addClass('auth-form')->id('passkey-creation-form') !!}

        <h1 class="auth-title">{{ __('Create a passkey') }}</h1>

        <p class="alert alert-info">@lang('You are logged in, but you need to create a passkey for future logins.')</p>
        <x-users::status />

        {!! BootForm::text(__('Passkey name'), 'name')->addClass('form-control-lg')->autofocus(true)->required() !!}

        <div class="mb-3 d-grid">
            <button class="btn btn-lg btn-primary" type="submit">
                <i class="bi bi-key-fill me-2"></i>
                @lang('Create passkey')
            </button>
        </div>

        {!! BootForm::close() !!}

        <x-users::back-to-website-link />
    </div>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            let form = document.getElementById('passkey-creation-form');
            const apiTokenElement = document.head.querySelector('meta[name="api-token"]');
            const csrfTokenElement = document.head.querySelector('meta[name="csrf-token"]');
            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                const responseOptions = await fetch('/api/passkeys/generate-options', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        Authorization: `Bearer ${apiTokenElement.content}`,
                        'X-CSRF-TOKEN': csrfTokenElement.content,
                    }
                });
                const options = await responseOptions.json();
                const startAuthenticationResponse = await window.startRegistration(options);

                const response = await fetch('/api/passkeys', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        Authorization: `Bearer ${apiTokenElement.content}`,
                        'X-CSRF-TOKEN': csrfTokenElement.content,
                    },
                    body: JSON.stringify({
                        options: JSON.stringify(options),
                        passkey: JSON.stringify(startAuthenticationResponse),
                    }),
                });

                if (response.ok) {
                    window.location.href = '/admin';
                }
            });
        });
    </script>
@endpush
