<div class="col-xs-6 col-sm-3 col-md-2 sidebar sidebar-offcanvas" id="sidebar" role="navigation">

    @if (TypiCMS::hasLogo())
        <a href="{{ route('dashboard') }}" title="{{ config('typicms.' . config('typicms.admin_locale') . '.website_title') }}">
            <img class="sidebar-logo img-responsive" src="{{ url('uploads/settings/'.config('typicms.image')) }}" width="167" alt="{{ TypiCMS::title() }}">
        </a>
    @endif

    {!! $sidebar->render() !!}

    <div class="made-by-wf">
        <span>Made by </span>
        <a href="http://webfactory.bg/" title="Webdesign & development by Webfactory" target="_blank"><img src="/uploads/settings/WF_Logo.svg" alt="webfactory" width="40"></a>
    </div>
</div>
