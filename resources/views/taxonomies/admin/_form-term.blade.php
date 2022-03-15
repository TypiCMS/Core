@component('core::admin._buttons-form', ['model' => $model])
@endcomponent

{!! BootForm::hidden('id') !!}

@include('core::form._title-and-slug')
