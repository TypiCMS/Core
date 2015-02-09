<div class="row">
    <div class="col-md-6 form-group">
        {!! BootForm::text(trans('validation.attributes.title'), $lang.'[title]') !!}
    </div>
    @include('core::form._slug')
</div>
