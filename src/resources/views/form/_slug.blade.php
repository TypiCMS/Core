@foreach ($locales as $lang)
<div class="form-group form-group-translation @if($errors->has($lang.'.slug'))has-error @endif">
    {!! Form::label('<span>'.trans('validation.attributes.slug').'</span> <span>('.$lang.')</span>')->addClass('control-label')->forId($lang . '[slug]') !!}
    <span></span>
    <div class="input-group">
        {!! Form::text($lang . '[slug]')->addClass('form-control')->id($lang . '[slug]')->data('slug', $lang . '[title]')->data('language', $lang) !!}
        <span class="input-group-btn">
            <button class="btn btn-default btn-slug @if($errors->has($lang.'.slug'))btn-danger @endif" type="button">@lang('validation.attributes.generate')</button>
        </span>
    </div>
    {!! $errors->first($lang.'.slug', '<p class="help-block">:message</p>') !!}
</div>
@endforeach
