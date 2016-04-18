<v-client-table :data="tableData" :columns="columns" :options="options"></v-client-table>
@section('js')
<script>
    vm.tableData = {!! News::allFiltered(config('typicms.news.select', '*'))['data'] !!};
</script>
@endsection
