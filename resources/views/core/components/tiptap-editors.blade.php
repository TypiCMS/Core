@props(['model', 'name', 'label' => null])

@foreach (locales() as $locale)
    <tiptap-editor name="{{ $name }}[{{ $locale }}]" locale="{{ $locale }}" init-content="{{ $model->getTranslation($name, $locale) }}" :label="'{{ $label }}'"></tiptap-editor>
@endforeach
