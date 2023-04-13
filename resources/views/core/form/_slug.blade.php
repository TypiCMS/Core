@foreach ($locales as $lang)
<div class="mb-3 form-group-translation @if ($errors->has('slug.'.$lang))has-error @endif">
    {!! Form::label('<span>'.__('Slug').'</span> <span>('.$lang.')</span>')->addClass('form-label')->forId('slug['.$lang.']') !!}
    <span></span>
    <div class="input-group">
        {!! Form::text('slug['.$lang.']')->addClass('form-control')->addClass($errors->has('slug.'.$lang) ? 'is-invalid' : '')->id('slug['.$lang.']')->data('slug', 'title['.$lang.']')->data('language', $lang) !!}
        <button class="btn btn-outline-secondary btn-slug" type="button">{{ __('Generate') }}</button>
        {!! $errors->first('slug.'.$lang, '<div class="invalid-feedback">:message</div>') !!}
    </div>
</div>
@endforeach
