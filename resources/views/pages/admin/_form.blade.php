@push('js')
    <script src="{{ asset('components/ckeditor4/ckeditor.js') }}"></script>
    <script src="{{ asset('components/ckeditor4/config-full.js') }}"></script>
@endpush

<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Pages')])
    @include('core::admin._title', ['default' => __('New page')])
    @component('core::admin._buttons-form', ['model' => $model])
    @endcomponent
</div>

<div class="content">

    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}

    <div class="row">

        <div class="col-lg-8">

            <div class="row gx-3">
                <div class="col-md-6">
                    {!! TranslatableBootForm::text(__('Title'), 'title') !!}
                </div>
                <div class="col-md-6">
                @foreach ($locales as $lang)
                    <div class="mb-3 form-group-translation">
                        <label class="form-label" for="slug[{{ $lang }}]"><span>{{ __('Url') }}</span> ({{ $lang }})</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $model->present()->parentUri($lang) }}</span>
                            <input class="form-control @if ($errors->has('slug.'.$lang))is-invalid @endif" type="text" name="slug[{{ $lang }}]" id="slug[{{ $lang }}]" value="{{ $model->translate('slug', $lang) }}" data-slug="title[{{ $lang }}]" data-language="{{ $lang }}">
                            <button class="btn btn-outline-dark btn-slug" type="button">{{ __('Generate') }}</button>
                            {!! $errors->first('slug.'.$lang, '<div class="invalid-feedback">:message</div>') !!}
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
            {!! TranslatableBootForm::hidden('uri') !!}
            <div class="mb-3">
                {!! TranslatableBootForm::hidden('status')->value(0) !!}
                {!! TranslatableBootForm::checkbox(__('Published'), 'status') !!}
            </div>
            {!! TranslatableBootForm::textarea(__('Body'), 'body')->addClass('ckeditor-full') !!}

            @can('read page_sections')
            @if ($model->id)
            <item-list
                url-base="/api/pages/{{ $model->id }}/sections"
                fields="id,image_id,page_id,position,status,title"
                table="page_sections"
                title="sections"
                include="image"
                :sub-list="true"
                :searchable="['title']"
                :sorting="['position']">

                <template slot="add-button" v-if="$can('create page_sections')">
                    @include('core::admin._button-create', ['url' => route('admin::create-page_section', $model->id), 'module' => 'page_sections'])
                </template>

                <template slot="columns" slot-scope="{ sortArray }">
                    <item-list-column-header name="checkbox" v-if="$can('update page_sections')||$can('delete page_sections')"></item-list-column-header>
                    <item-list-column-header name="edit" v-if="$can('update page_sections')"></item-list-column-header>
                    <item-list-column-header name="status_translated" sortable :sort-array="sortArray" :label="$t('Status')"></item-list-column-header>
                    <item-list-column-header name="position" sortable :sort-array="sortArray" :label="$t('Position')"></item-list-column-header>
                    <item-list-column-header name="image" :label="$t('Image')"></item-list-column-header>
                    <item-list-column-header name="title_translated" sortable :sort-array="sortArray" :label="$t('Title')"></item-list-column-header>
                </template>

                <template slot="table-row" slot-scope="{ model, checkedModels, loading }">
                    <td class="checkbox" v-if="$can('update page_sections')||$can('delete page_sections')"><item-list-checkbox :model="model" :checked-models-prop="checkedModels" :loading="loading"></item-list-checkbox></td>
                    <td v-if="$can('update page_sections')"><item-list-edit-button :url="'/admin/pages/'+model.page_id+'/sections/'+model.id+'/edit'"></item-list-edit-button></td>
                    <td><item-list-status-button :model="model"></item-list-status-button></td>
                    <td><item-list-position-input :model="model"></item-list-position-input></td>
                    <td><img :src="model.thumb" alt="" height="27"></td>
                    <td v-html="model.title_translated"></td>
                </template>

            </item-list>
            @endif
            @endcan

        </div>

        <div class="col-lg-4">
            <div class="bg-light p-4">
                @if ($model->redirect !== 1)
                    <file-manager related-table="{{ $model->getTable() }}" :related-id="{{ $model->id ?? 0 }}"></file-manager>
                    <file-field type="image" field="image_id" :init-file="{{ $model->image ?? 'null' }}"></file-field>
                    <files-field :init-files="{{ $model->files }}"></files-field>
                    {!! TranslatableBootForm::text(__('Meta keywords'), 'meta_keywords') !!}
                    {!! TranslatableBootForm::text(__('Meta description'), 'meta_description') !!}
                @endif

                <div class="mb-3">
                    @if ($model->redirect !== 1)
                        {!! BootForm::hidden('is_home')->value(0) !!}
                        {!! BootForm::checkbox(__('Is home'), 'is_home') !!}
                        {!! BootForm::hidden('private')->value(0) !!}
                        {!! BootForm::checkbox(__('Private'), 'private') !!}
                    @endif
                    {!! BootForm::hidden('redirect')->value(0) !!}
                    {!! BootForm::checkbox(__('Redirect to first child'), 'redirect') !!}
                </div>
                @if ($model->redirect !== 1)
                    {!! BootForm::select(__('Module'), 'module', TypiCMS::getModulesForSelect())->disable($model->subpages->count() > 0)->formText($model->subpages->count() ? __('A page containing subpages cannot be linked to a module') : '') !!}
                    {!! BootForm::select(__('Template'), 'template', TypiCMS::pageTemplates()) !!}
                    @if (!$model->id)
                    {!! BootForm::select(__('Add to menu'), 'add_to_menu', ['' => ''] + Menus::all()->pluck('name', 'id')->all(), null, ['class' => 'form-control']) !!}
                    @endif
                    {!! BootForm::textarea(__('Css'), 'css') !!}
                    {!! BootForm::textarea(__('Js'), 'js') !!}
                @endif
            </div>
        </div>

    </div>

</div>
