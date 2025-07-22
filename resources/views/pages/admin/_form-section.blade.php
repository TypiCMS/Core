<div class="header">
    @include('core::admin._button-back', ['url' => $page->editUrl(), 'title' => $page->title])
    @include('core::admin._title', ['default' => __('New page section')])
    @component('core::admin._buttons-form', ['model' => $model])
    @endcomponent
</div>

<div class="content">
    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}
    {!! BootForm::hidden('page_id')->value($page->id) !!}

    <file-manager related-table="{{ $model->getTable() }}" :related-id="{{ $model->id ?? 0 }}"></file-manager>
    <file-field type="image" field="image_id" :init-file="{{ $model->image ?? 'null' }}"></file-field>
    <files-field :init-files="{{ $model->files }}"></files-field>

    @include('core::form._title-and-slug')
    <div class="mb-3">
        {!! TranslatableBootForm::hidden('status')->value(0) !!}
        {!! TranslatableBootForm::checkbox(__('Published'), 'status') !!}
    </div>
    <div class="mb-3">
        {!! BootForm::hidden('hide_title')->value(0) !!}
        {!! BootForm::checkbox(__('No title'), 'hide_title') !!}
    </div>
    <div class="row">
        <div class="col-md-6">
            {!! BootForm::select(__('Template'), 'template', pageSectionTemplates()) !!}
        </div>
    </div>
    <x-core::tiptap-editors :model="$model" name="body" :label="__('Body')" />
</div>
