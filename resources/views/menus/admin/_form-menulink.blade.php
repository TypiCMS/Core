<div class="header">
    @include('core::admin._button-back', ['url' => $menu->editUrl(), 'title' => $menu->name])
    @include('core::admin._title', ['default' => __('New menulink')])
    @component('core::admin._buttons-form', ['model' => $model, 'langSwitcher' => true])
    @endcomponent
</div>

<div class="content">

    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}
    {!! BootForm::hidden('menu_id')->value($menu->id) !!}
    {!! BootForm::hidden('position') !!}
    {!! BootForm::hidden('parent_id') !!}

    <div class="row gx-3">

        <div class="col-sm-6">
            {!! TranslatableBootForm::text(__('Title'), 'title') !!}
            <div class="mb-3">
                {!! TranslatableBootForm::hidden('status')->value(0) !!}
                {!! TranslatableBootForm::checkbox(__('Published'), 'status') !!}
            </div>
            {!! TranslatableBootForm::textarea(__('Description'), 'description')->rows(3) !!}
            <file-manager related-table="{{ $model->getTable() }}" :related-id="{{ $model->id ?? 0 }}"></file-manager>
            <file-field type="image" field="image_id" :init-file="{{ $model->image ?? 'null' }}"></file-field>
        </div>

        <div class="col-sm-6">
            {!! BootForm::select(__('Page'), 'page_id', Pages::allForSelect()) !!}
            {!! BootForm::select(__('Section'), 'section_id', ['' => '']) !!}
            {!! TranslatableBootForm::text(__('Url'), 'url')->placeholder('http://') !!}
            {!! BootForm::select(__('Target'), 'target', ['' => __('Active tab'), '_blank' => __('New tab')]) !!}
            {!! BootForm::text(__('Class'), 'class') !!}
        </div>

    </div>
    @push('js')
    <script>
    var selectPage = document.getElementById('page_id');
    var selectSection = document.getElementById('section_id');
    var selectedSectionId = parseInt('{{ $model->section_id }}');
    function initSelect() {
        for (var i = 0; i < selectSection.length; i++) {
            if (selectSection.options[i].value !== '') {
                selectSection.remove(i);
            }
        }
    }
    function getSections() {
        initSelect();
        var pageId = selectPage.options[selectPage.selectedIndex].value;
        if (!pageId) {
            return;
        }

        // Get sections and create <option> elements.
        axios.get('/api/pages/'+pageId+'/sections?sort=position&fields[page_sections]=id,position,title').then(function(response){
            var sections = response.data.data;
            for (var i = 0; i < sections.length; i++) {
                var option = document.createElement('option');
                option.value = sections[i].id;
                option.innerHTML = sections[i].title_translated+' (#'+sections[i].id+')';
                if (sections[i].id === selectedSectionId) {
                    option.selected = true;
                }
                selectSection.appendChild(option);
            }
        });
    }
    selectPage.onchange = getSections;
    getSections();
    </script>
    @endpush

</div>
