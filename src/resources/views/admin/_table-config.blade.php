<script>
    var tablesConfig = {
        compileTemplates: true,
        highlightMatches: true,
        pagination: {
            // dropdown:true
            // chunk:5
        },
        texts: {
            count: "@lang('global.table.count Records')",
            filter: "@lang('global.table.Filter Results:')",
            filterPlaceholder: "@lang('global.table.Search query')",
            limit: "@lang('global.table.Records:')",
            page: "@lang('global.table.Page:')",
            noResults: "@lang('global.table.No matching records')",
            filterBy: "@lang('global.table.Filter by column')"
        },
        skin:'table-condensed'
    };
    options.templates = {
        id: '<a href="javascript:void(0);" @click="$parent.deleteMe({id})"><span class="fa fa-remove"></span></a>&nbsp;&nbsp;&nbsp;<a class="btn btn-default btn-xs" href="{{ url()->current() }}/{id}/edit">Edit</i></a>',
        status: '<div @click="$parent.toggle({id})">' +
            '<span class="fa switch" :class="{status} ? \'fa-toggle-on\' : \'fa-toggle-off\'"></span>' +
        '</div>',
        thumb: '<img src="{thumb}">'
    };
    options.perPage = 25;
    options.perPageValues = [25, 50, 100, 500, 1000, 5000];
    options.dateFormat = 'yyyy mm dd';
    options.headings = Object.assign({}, options.headings, {
        id: '',
        status: '@lang("validation.attributes.status")',
        thumb: '@lang("validation.attributes.image")',
        date: '@lang("validation.attributes.date")',
        title: '@lang("validation.attributes.title")',
    });

</script>
