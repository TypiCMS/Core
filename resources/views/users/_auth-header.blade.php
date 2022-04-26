<div class="auth-header">
    @if (TypiCMS::hasLogo())
        <img class="auth-header-logo" src="{{ Storage::url('settings/'.config('typicms.image')) }}" width="" height="{{ 80 }}" alt="{{ TypiCMS::title() }}">
    @else
        <h1 class="auth-header-title">{{ config('app.name') }}</h1>
    @endif
</div>
