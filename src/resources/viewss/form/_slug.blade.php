<div class="col-md-6 @if($errors->has($lang.'.slug'))has-error @endif">
    {!! Form::label(trans('validation.attributes.slug'))->addClass('control-label')->forId($lang . '[slug]') !!}
    <div class="input-group">
        {!! Form::text($lang . '[slug]')->addClass('form-control')->id($lang . '[slug]')->data('slug', $lang . '[title]') !!}
        <span class="input-group-btn">
            <button class="btn btn-default btn-slug @if($errors->has($lang.'.slug'))btn-danger @endif" type="button">@lang('validation.attributes.generate')</button>
        </span>
    </div>
    {!! $errors->first($lang.'.slug', '<p class="help-block">:message</p>') !!}
</div>
