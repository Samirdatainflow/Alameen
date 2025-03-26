// dataTable
var CustomerReportList = $('#CustomerReportList').DataTable({
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
        "url": base_url+"/report/customer-report-list",
        "type": "POST",
        'data': function(data){
            data.filter_reg_no=$("#filter_reg_no").val();
            data.filter_customer_area=$("#filter_customer_area").val();
            data.filter_customer_region=$("#filter_customer_region").val();
            data.filter_customer_teritory=$("#filter_customer_teritory").val();
            data.filter_status=$("#filter_status").val();
        },
        
    },
    'columns': [
        {data: 'client_id', name: 'client_id', orderable: true, searchable: false},
        {data: 'reg_no', name: 'reg_no', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'customer_region', name: 'customer_region', orderable: false, searchable: false},
        {data: 'customer_teritory', name: 'customer_teritory', orderable: false, searchable: false},
        {data: 'customer_area', name: 'customer_area', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$("#filter_reg_no").on('keyup input',function(){
    CustomerReportList.draw();
});
$("#filter_customer_area").on('keyup input',function(){
    CustomerReportList.draw();
});
$("#filter_customer_region").on('keyup input',function(){
    CustomerReportList.draw();
});
$("#filter_customer_teritory").on('keyup input',function(){
    CustomerReportList.draw();
});
$('body').on('click', '.reset-filter', function() {
    $('#filter_reg_no').val('');
    $('#filter_customer_area').val('');
    $('#filter_customer_region').val('');
    $('#filter_customer_teritory').val('');
    CustomerReportList.draw();
});
// Top 5 High Ordered Customer in Number
var Top5HighOrderedCustomerNumberList = $('#Top5HighOrderedCustomerNumberList').DataTable({
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
        "url": base_url+"/report/customer-report/top-5-high-ordered-customer-number-list",
        "type": "POST",
        'data': function(data){
            //
        },
        
    },
    'columns': [
        {data: 'client_id', name: 'client_id', orderable: true, searchable: false},
        {data: 'reg_no', name: 'reg_no', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'product_qty', name: 'product_qty', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Top 5 High Ordered Customer in Value
var Top5HighOrderedCustomerNumberList = $('#Top5HighOrderedCustomerValueList').DataTable({
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
        "url": base_url+"/report/customer-report/top-5-high-ordered-customer-value-list",
        "type": "POST",
        'data': function(data){
            //
        },
        
    },
    'columns': [
        {data: 'client_id', name: 'client_id', orderable: true, searchable: false},
        {data: 'reg_no', name: 'reg_no', orderable: false, searchable: false},
        {data: 'customer_name', name: 'customer_name', orderable: false, searchable: false},
        {data: 'productAmount', name: 'productAmount', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Customer Report By Inventory
var CustomerReportByInventoryList = $('#CustomerReportByInventoryList').DataTable({
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
        "url": base_url+"/report/customer-report/customer-report-by-inventory-list",
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