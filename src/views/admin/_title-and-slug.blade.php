<div class="row">
    <div class="col-md-6 form-group">
        {!! BootForm::text(trans('validation.attributes.title'), $lang.'[title]') !!}
    </div>
    <div class="col-md-6 form-group @if($errors->has($lang.'.slug'))has-error @endif">
        <label class="control-label" for="{{ $lang }}[slug]">@lang('validation.attributes.slug')</label>
        <div class="input-group">
            <input class="form-control" type="text" name="{{ $lang }}[slug]" id="{{ $lang }}[slug]" value="@if($model->hasTranslation($lang)){{ $model->translate($lang)->slug }}@endif">
            <span class="input-group-btn">
                <button class="btn btn-default btn-slug @if($errors->has($lang.'.slug'))btn-danger @endif" type="button">@lang('validation.attributes.generate')</button>
            </span>
        </div>
        {!! $errors->first($lang.'.slug', '<p class="help-block">:message</p>') !!}
    </div>
</div>
