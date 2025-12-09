<div class="header">
    <x-core::back-button :url="route('admin::index-terms', $taxonomy)" :title="__('Terms')" />
    <x-core::title :$model :default="__('New term')" />
    <x-core::form-buttons :$model />
</div>

<div class="content">
    <x-core::form-errors />

    <x-core::title-and-slug-fields />
</div>
