var ReceivingPutAwayReportList = $('#ReceivingPutAwayReportList').DataTable({
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
    "searching": false,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/receiving-put-away-report-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'order_number', name: 'order_number', orderable: true, searchable: false},
        {data: 'order_date_show', name: 'order_date_show', orderable: false, searchable: false},
        {data: 'courier_number', name: 'courier_number', orderable: false, searchable: false},
        {data: 'courier_date_show', name: 'courier_date_show', orderable: false, searchable: false},
        {data: 'vehicle_no', name: 'vehicle_no', orderable: false, searchable: false},
        {data: 'no_of_box', name: 'no_of_box', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Number of picked order in a Date
var NumberofpickedorderinAdateList = $('#NumberofpickedorderinAdateList').DataTable({
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
    "searching": false,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/receiving-put-away-report/number-of-picked-order-in-a-date-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'order_number', name: 'order_number', orderable: true, searchable: false},
        {data: 'order_date_show', name: 'order_date_show', orderable: false, searchable: false},
        {data: 'courier_number', name: 'courier_number', orderable: false, searchable: false},
        {data: 'vehicle_no', name: 'vehicle_no', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Number of picked order in a Date
var GoodBadQuantityInOrder = $('#GoodBadQuantityInOrder').DataTable({
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
    "searching": false,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/receiving-put-away-report/good-or-bad-quantity-order-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: false, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'good_quantity', name: 'good_quantity', orderable: false, searchable: false},
        {data: 'bad_quantity', name: 'bad_quantity', orderable: true, searchable: false},
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Number of picked order in a Date
var BinningReport = $('#BinningReport').DataTable({
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
    "searching": false,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/receiving-put-away-report/binning-report-list",
        "type": "POST",
        'data': function(data){
            data.filter_user=$("#filter_user").val();
            data.filter_status=$("#filter_status").val();
        },
        
    },
    'columns': [
        {data: 'binning_task_id', name: 'binning_task_id', orderable: false, searchable: false},
        {data: 'binning_date', name: 'binning_date', orderable: false, searchable: false},
        {data: 'user_name', name: 'user_name', orderable: false, searchable: false},
        {data: 'items', name: 'items', orderable: true, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$("#filter_user").on('change',function(){
    BinningReport.draw();
});
$("#filter_status").on('change',function(){
    BinningReport.draw();
});
$(".reset-filter").on('click',function(){
    $("#filter_user").val('');
    $("#filter_status").val('');
    BinningReport.draw();
});