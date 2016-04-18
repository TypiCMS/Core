<v-client-table :data="tableData" :columns="columns" :options="options"></v-client-table>
@section('js')
<script>
    vm.tableData = {!! $data['data'] !!};
</script>
@endsection
