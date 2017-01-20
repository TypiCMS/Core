@foreach ($locales as $lang)
<div class="form-group form-group-translation @if($errors->has('slug.'.$lang))has-error @endif">
    {!! Form::label('<span>'.__('Slug').'</span> <span>('.$lang.')</span>')->addClass('control-label')->forId('slug['.$lang.']') !!}
    <span></span>
    <div class="input-group">
        {!! Form::text('slug['.$lang.']')->addClass('form-control')->id('slug['.$lang.']')->data('slug', 'title['.$lang.']')->data('language', $lang) !!}
        <span class="input-group-btn">
            <button class="btn btn-default btn-slug @if($errors->has('slug.'.$lang))btn-danger @endif" type="button">{{ __('Generate') }}</button>
        </span>
    </div>
    {!! $errors->first('slug.'.$lang, '<p class="help-block">:message</p>') !!}
</div>
@endforeach
