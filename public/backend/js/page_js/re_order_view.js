// List
var OrderRequest = $('#OrderRequestList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "ordering":true,
    "responsive": true,
    "order": [0, ''],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-re-order",
        "type": "POST",
        'data': function(data){
          data.filter_supplier=$("#filter_supplier").val();
        },
        
    },
    'columns': [
        {data: 'part_name', name: 'part_name', orderable: true, searchable: false},
        {data: 'pmpno', name: 'pmpno', orderable: false, searchable: false},
        {data: 'mad', name: 'mad', orderable: false, searchable: false},
        {data: 'transit_quantity', name: 'transit_quantity', orderable: false, searchable: false},
        {data: 'reorder', name: 'reorder', orderable: false, searchable: false},
    ],
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});

$('div.toolbar').html('<button type="button" aria-haspopup="true" onclick="ExportTable()" aria-expanded="false" class="btn-shadow btn btn-info" title="Export"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-download fa-w-20"></i></span>Export</button>');