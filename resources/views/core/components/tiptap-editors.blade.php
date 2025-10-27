@foreach (locales() as $locale)
    <tiptap-editor name="{{ $name }}[{{ $locale }}]" locale="{{ $locale }}" init-content="{{ $model->getTranslation($name, $locale) }}" :label="'{{ $label ?? null }}'"></tiptap-editor>
@endforeach
