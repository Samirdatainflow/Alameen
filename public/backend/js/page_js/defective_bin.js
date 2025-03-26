// List Delivery Method
var DirectReturnBin = $('#DirectReturnBinList').DataTable({
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
        "url": base_url+"/direct-return-bin-list",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'pmpno', name: 'pmpno', orderable: true, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'order_id', name: 'order_id', orderable: false, searchable: false},
        {data: 'full_name', name: 'full_name', orderable: false, searchable: false},
        {data: 'bad_quantity', name: 'bad_quantity', orderable: false, searchable: false},
        {data: 'location_name', name: 'location_name', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});

var DirectReturnBin = $('#CustomerReturnBinList').DataTable({
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
        "url": base_url+"/customer-return-bin-list",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'pmpno', name: 'pmpno', orderable: true, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'bad_quantity', name: 'bad_quantity', orderable: false, searchable: false},
        {data: 'order_id', name: 'order_id', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false}
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});