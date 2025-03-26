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
        "url": base_url+"/report/list-purchase-order-outstanding-report",
        "type": "POST",
        'data': function(data){
            data.filter_date=$("#filter_outstanding_date").val();
            data.filter_supplier=$("#filter_outstanding_supplier").val();
        },
        
    },
    'columns': [
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false},
        {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$('#filter_outstanding_date').datepicker().change(function(){
    OutstandingReportList.draw();
});
$('#filter_outstanding_supplier').change(function(){
    OutstandingReportList.draw();
});
$('body').on('click', '.reset-outstanding-filter', function() {
    $('#filter_outstanding_supplier').val('');
    $('#filter_outstanding_date').val('');
    OutstandingReportList.draw();
});

var PurchaseOrderReportList = $('#PurchaseOrderReportList').DataTable({
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
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/purchase-order-report-list",
        "type": "POST",
        'data': function(data){
          data.filter_from_date=$("#filter_from_date").val();
          data.filter_to_date=$("#filter_to_date").val();
          data.filter_supplier=$("#filter_supplier").val();
          data.filter_warehouse=$("#filter_warehouse").val();
        },
        
    },
    'columns': [
        {data: 'order_id', name: 'order_id', orderable: true, searchable: false},
        {data: 'order_date', name: 'order_date', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'supplier', name: 'supplier', orderable: false, searchable: false},
        {data: 'item', name: 'item', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$("#filter_category").on('keyup input',function(){
    InventoryReport.draw();
});
$('#filter_from_date').datepicker().change(function(){
    PurchaseOrderReportList.draw();
});
$('#filter_to_date').datepicker().change(function(){
    PurchaseOrderReportList.draw();
});
$('#filter_supplier').selectpicker().change(function(){
    PurchaseOrderReportList.draw();
});
$('#filter_warehouse').selectpicker().change(function(){
    PurchaseOrderReportList.draw();
});
$('body').on('click', '.reset-filter', function() {
    $('#filter_from_date').val('');
    $('#filter_to_date').val('');
    $("#filter_supplier").val('').selectpicker("refresh");
    $("#filter_warehouse").val('').selectpicker("refresh");
    PurchaseOrderReportList.draw();
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
        "url": base_url+"/report/list-purchase-order-ageing-report",
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
        "url": base_url+"/report/list-purchase-order-invoice-report",
        "type": "POST",
        'data': function(data){
            data.filter_month=$("#filter_invoice_month").val();
            data.filter_supplier=$("#filter_invoice_supplier").val();
        },
        
    },
    'columns': [
        {data: 'full_name', name: 'full_name', orderable: false, searchable: false},
        {data: 'invoice_date', name: 'invoice_date', orderable: false, searchable: false},
        {data: 'due_days', name: 'due_days', orderable: false, searchable: false},
        {data: 'grand_total', name: 'grand_total', orderable: false, searchable: false},
        {data: 'due_amount', name: 'due_amount', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$('#filter_invoice_supplier').change(function(){
    InvoiceReportList.draw();
});
$('#filter_invoice_month').change(function(){
    InvoiceReportList.draw();
});
$('body').on('click', '.reset-invoice-filter', function() {
    $('#filter_invoice_supplier').val('');
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




