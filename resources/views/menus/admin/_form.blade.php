<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Menus')])
    @include('core::admin._title', ['default' => __('New menu')])
    @component('core::admin._buttons-form', ['model' => $model])
    @endcomponent
</div>

<div class="content">

    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}

    <file-manager related-table="{{ $model->getTable() }}" :related-id="{{ $model->id ?? 0 }}"></file-manager>
    <file-field type="image" field="image_id" :init-file="{{ $model->image ?? 'null' }}"></file-field>

    @if ($model->id)
    {!! BootForm::hidden('name') !!}
    @else
    {!! BootForm::text(__('Name'), 'name')->required() !!}
    @endif

    {!! BootForm::text(__('Class'), 'class') !!}
    <div class="mb-3">
        {!! TranslatableBootForm::hidden('status')->value(0) !!}
        {!! TranslatableBootForm::checkbox(__('Published'), 'status') !!}
    </div>

    @if ($model->id)

        <item-list-tree
            url-base="/api/menus/{{ $model->id }}/menulinks"
            fields="id,menu_id,page_id,position,parent_id,status,title,url"
            table="menulinks"
            title="Menulinks"
            v-if="$can('read menulinks')"
            :sub-list="true"
        >

            <template slot="add-button" v-if="$can('create menulinks')">
                @include('core::admin._button-create', ['url' => route('admin::create-menulink', $model->id), 'module' => 'menus'])
            </template>

        </item-list-tree>

    @endif

</div>
