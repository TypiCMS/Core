<div class="auth-container-logo">
    @if (TypiCMS::hasLogo())
        @include('core::public._logo', ['height' => 50])
    @else
        <h1>{{ config('app.name') }}</h1>
    @endif
</div>
