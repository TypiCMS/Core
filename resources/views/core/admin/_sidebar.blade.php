<div class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="offcanvasResponsive" aria-labelledby="offcanvasResponsiveLabel">
    <div class="offcanvas-header">
        <button class="btn btn-dark" type="button" data-bs-dismiss="offcanvas" data-bs-target="#offcanvasResponsive" aria-controls="offcanvasResponsive" aria-label="{{ __('Close navigation') }}">
            Close
        </button>
    </div>
    <div class="offcanvas-body">
        {!! $sidebar->render() !!}
    </div>
</div>
