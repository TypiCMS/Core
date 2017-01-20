<div class="row">
    <div class="col-md-6">
        {!! TranslatableBootForm::text(__('validation.attributes.title'), 'title') !!}
    </div>
    <div class="col-sm-6">
        @include('core::form._slug')
    </div>
</div>
