var SalesOrderReportList = $('#SalesOrderReportList').DataTable({
    "dom": "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-4'f><'col-sm-12 col-md-6 text-right'B>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    buttons: [
            {
                extend:    'pdfHtml5',
                text:      '<i class="fa fa-file-pdf-o fa-w-20"></i> Export PDF',
                className: 'btn-shadow btn btn-datatable'
            },
            {
                extend:    'csvHtml5',
                text:      '<i class="fa fa-file-excel-o fa-w-20"></i> Export CSV',
                className: 'btn-shadow btn btn-datatable'
            },
            {
                extend:    'print',
                text:      '<i class="fa fa-print fa-w-20"></i> Export Print',
                className: 'btn-shadow btn btn-datatable'
            },
        ],
    "processing": true,
    "serverSide": true,
    "responsive": true,
    "pageLength" : 10,
    "order": [3, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/sales-order-report-list",
        "type": "POST",
        'data': function(data){
            //
        },
        
    },
    'columns': [
        {data: 'client_id', name: 'client_id', orderable: true, searchable: false},
        {data: 'reg_no', name: 'reg_no', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'pmpno', name: 'pmpno', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});