
var CustomerOrderList = $('#CustomerOrderList').DataTable({
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
    "order": [0, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/sales-order/customer-order-report-list",
        "type": "POST",
        'data': function(data){
            data.filter_from_date=$("#filter_from_date").val();
            data.filter_to_date=$("#filter_to_date").val();
        },
        
    },
    'columns': [
        {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
        {data: 'reg_no', name: 'reg_no', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'pmpno', name: 'pmpno', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$('#filter_from_date').datepicker().change(function(){
    CustomerOrderList.draw();
});
$('#filter_to_date').datepicker().change(function(){
    CustomerOrderList.draw();
});
$('body').on('click', '.reset-filter', function() {
    $('#filter_from_date').val('');
    $('#filter_to_date').val('');
    CustomerOrderList.draw();
});
// Approved Order
var ApprovedOrdersList = $('#ApprovedOrdersList').DataTable({
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
    "order": [0, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/sales-order-report/approved-orders-list",
        "type": "POST",
        'data': function(data){
            data.filter_area=$("#filter_area").val();
            data.filter_territory=$("#filter_territory").val();
            data.filter_region=$("#filter_region").val();
        },
        
    },
    'columns': [
        {data: 'created_at', name: 'created_at', orderable: true, searchable: false},
        {data: 'reg_no', name: 'reg_no', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'customer_area', name: 'customer_area', orderable: false, searchable: false},
        {data: 'customer_teritory', name: 'customer_teritory', orderable: true, searchable: false},
        {data: 'customer_region', name: 'customer_region', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$('#filter_area').on('keyup input', function(){
    ApprovedOrdersList.draw();
});
$('#filter_territory').on('keyup input', function(){
    ApprovedOrdersList.draw();
});
$('#filter_region').on('keyup input', function(){
    ApprovedOrdersList.draw();
});
$('body').on('click', '.reset-approved-orders-filter', function() {
    $('#filter_area').val('');
    $('#filter_territory').val('');
    $('#filter_region').val('');
    ApprovedOrdersList.draw();
});
// Not Approved Order
var NotApprovedOrdersList = $('#NotApprovedOrdersList').DataTable({
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
    "order": [0, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/sales-order-report/not-approved-orders-list",
        "type": "POST",
        'data': function(data){
            data.not_approved_filter_area=$("#not_approved_filter_area").val();
            data.not_approved_filter_territory=$("#not_approved_filter_territory").val();
            data.not_approved_filter_region=$("#not_approved_filter_region").val();
        },
        
    },
    'columns': [
        {data: 'created_at', name: 'created_at', orderable: true, searchable: false},
        {data: 'reg_no', name: 'reg_no', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'customer_area', name: 'customer_area', orderable: false, searchable: false},
        {data: 'customer_teritory', name: 'customer_teritory', orderable: true, searchable: false},
        {data: 'customer_region', name: 'customer_region', orderable: false, searchable: false},
        {data: 'reason', name: 'reason', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$('#not_approved_filter_area').on('keyup input', function(){
    NotApprovedOrdersList.draw();
});
$('#not_approved_filter_territory').on('keyup input', function(){
    NotApprovedOrdersList.draw();
});
$('#not_approved_filter_region').on('keyup input', function(){
    NotApprovedOrdersList.draw();
});
$('body').on('click', '.reset-not-approved-orders-filter', function() {
    $('#not_approved_filter_area').val('');
    $('#not_approved_filter_territory').val('');
    $('#not_approved_filter_region').val('');
    NotApprovedOrdersList.draw();
});
// Not Approved Order
var NoofOrdersByDatesList = $('#NoofOrdersByDatesList').DataTable({
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
    "order": [0, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/sales-order-report/no-of-orders-by-dates-list",
        "type": "POST",
        'data': function(data){
            data.filter_from_date_by_days=$("#filter_from_date_by_days").val();
            data.filter_to_date_by_days=$("#filter_to_date_by_days").val();
        },
        
    },
    'columns': [
        {data: 'created_at', name: 'created_at', orderable: true, searchable: false},
        {data: 'order_id', name: 'order_id', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$('#filter_from_date_by_days').datepicker().change(function(){
    NoofOrdersByDatesList.draw();
});
$('#filter_to_date_by_days').datepicker().change(function(){
    NoofOrdersByDatesList.draw();
});
$('body').on('click', '.reset-no-of-orders-filter', function() {
    $('#filter_from_date_by_days').val('');
    $('#filter_to_date_by_days').val('');
    NoofOrdersByDatesList.draw();
});


// Outstanding Report
var OutstandingReportList = $('#OutstandingReportList').DataTable({
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
    "order": [0, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/list-sales-order-outstanding-report",
        "type": "POST",
        'data': function(data){
            data.filter_date=$("#filter_outstanding_date").val();
            data.filter_customer=$("#filter_outstanding_customer").val();
        },
        
    },
    'columns': [
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'invoice_amount', name: 'invoice_amount', orderable: false, searchable: false},
        // {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$('#filter_outstanding_date').datepicker().change(function(){
    OutstandingReportList.draw();
});
$('#filter_outstanding_customer').change(function(){
    OutstandingReportList.draw();
});
$('body').on('click', '.reset-outstanding-filter', function() {
    $('#filter_outstanding_customer').val('');
    $('#filter_outstanding_date').val('');
    OutstandingReportList.draw();
});

// Ageing Report
var AgeingReportList = $('#AgeingReportList').DataTable({
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
    "order": [0, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/list-sales-order-ageing-report",
        "type": "POST",
        'data': function(data){
            // data.filter_date=$("#filter_outstanding_date").val();
            // data.filter_customer=$("#filter_outstanding_customer").val();
        },
        
    },
    'columns': [
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'current_date', name: 'current_date', orderable: false, searchable: false},
        {data: 'duedays1', name: 'duedays1', orderable: false, searchable: false},
        {data: 'duedays2', name: 'duedays2', orderable: false, searchable: false},
        {data: 'duedays3', name: 'duedays3', orderable: false, searchable: false},
        {data: 'duedays4', name: 'duedays4', orderable: false, searchable: false},
        {data: 'total_amount', name: 'total_amount', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// $('#filter_outstanding_date').datepicker().change(function(){
//     OutstandingReportList.draw();
// });
// $('#filter_outstanding_customer').change(function(){
//     OutstandingReportList.draw();
// });
// $('body').on('click', '.reset-outstanding-filter', function() {
//     $('#filter_outstanding_customer').val('');
//     $('#filter_outstanding_date').val('');
//     OutstandingReportList.draw();
// });

// Invoice Report
var InvoiceReportList = $('#InvoiceReportList').DataTable({
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
    "order": [0, 'asc'],
    "searching": false,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/list-sales-order-invoice-report",
        "type": "POST",
        'data': function(data){
            data.filter_month=$("#filter_invoice_month").val();
            data.filter_customer=$("#filter_invoice_customer").val();
        },
        
    },
    'columns': [
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'invoice_no', name: 'invoice_no', orderable: false, searchable: false},
        {data: 'invoice_date', name: 'invoice_date', orderable: false, searchable: false},
        {data: 'due_days', name: 'due_days', orderable: false, searchable: false},
        {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
        {data: 'due_amount', name: 'due_amount', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// $('#filter_outstanding_date').datepicker().change(function(){
//     OutstandingReportList.draw();
// });
$('#filter_invoice_customer').change(function(){
    InvoiceReportList.draw();
});
$('#filter_invoice_month').change(function(){
    InvoiceReportList.draw();
});
$('body').on('click', '.reset-invoice-filter', function() {
    $('#filter_invoice_customer').val('');
    $('#filter_invoice_month').val('');
    InvoiceReportList.draw();
});


$('.datepicker').datepicker({
    format  :  'yyyy-mm-dd',
    todayHighlight: true,
    autoclose: true,
}).on('changeDate', function(e){
    $(this).datepicker('hide');
});
