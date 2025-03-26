// Table
var BarcodeList = $('#BarcodeList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6'<'toolbar'>>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/list-barcode-table-data",
        "type": "POST",
        'data': function(data){
          // console.log(data);
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: false, searchable: false},
        {data: 'part_no', name: 'part_no', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'full_name', name: 'full_name', orderable: false, searchable: false},
        {data: 'barcode_number', name: 'barcode_number', orderable: false, searchable: false},
        {data: 'invoice_no', name: 'invoice_no', orderable: false, searchable: false},
        {data: 'customer', name: 'customer', orderable: false, searchable: false},
        {data: 'date_of_invoice', name: 'date_of_invoice', orderable: false, searchable: false},
    ],
   
}).on('xhr.dt', function(e, settings, json, xhr) {
   
});
$('div.toolbar').html('<button type="button" aria-haspopup="true" aria-expanded="false" class="btn-shadow btn btn-info" onclick="BarcodeExport()"><span class="btn-icon-wrapper pr-2 opacity-7" ><i class="fa fa-download fa-w-20"></i></span>Export</button>');

function BarcodeExport() {
    window.location.href = base_url+"/barcode-list-export";
}

