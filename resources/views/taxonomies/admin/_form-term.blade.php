<div class="header">
    @include('core::admin._button-back', ['url' => route('admin::index-terms', $taxonomy), 'title' => __('Terms')])
    @include('core::admin._title', ['default' => __('New term')])
    @component('core::admin._buttons-form', ['model' => $model])
    @endcomponent
</div>

<div class="content">

    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}

    @include('core::form._title-and-slug')

</div>
