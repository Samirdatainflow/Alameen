
var InventoryReport = $('#InventoryReportList').DataTable({
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
    "pageLength" : 50,
    "order": [0, 'desc'],
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/inventory-report-list",
        "type": "POST",
        'data': function(data){
          data.filter_product_id=$("#filter_product_id").val();
          data.filter_part_no=$("#filter_part_no").val();
          data.filter_part_name=$("#filter_part_name").val();
          data.filter_category=$("#filter_category").val();
          data.filter_supplier=$("#filter_supplier").val();
          data.filter_warehouse=$("#filter_warehouse").val();
          data.filter_status=$("#filter_status").val();
        },
        
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: true, searchable: false},
        {data: 'part_no', name: 'part_no', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'ct', name: 'ct', orderable: false, searchable: false},
        {data: 'cost', name: 'cost', orderable: false, searchable: false},
        {data: 'pmrprc', name: 'pmrprc', orderable: false, searchable: false},
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'transit_quantity', name: 'transit_quantity', orderable: false, searchable: false},
        {data: 'damage_quantity', name: 'damage_quantity', orderable: false, searchable: false},
        {data: 'status', name: 'status', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$("#filter_product_id").on('keyup input',function(){
    InventoryReport.draw();
});
$("#filter_part_no").on('keyup input',function(){
    InventoryReport.draw();
});
$("#filter_part_name").on('change',function(){
    InventoryReport.draw();
});
$("#filter_category").on('keyup input',function(){
    InventoryReport.draw();
});
$('#filter_supplier').selectpicker().change(function(){
    InventoryReport.draw();
});
$('#filter_warehouse').selectpicker().change(function(){
    InventoryReport.draw();
});
$("#filter_status").on('change',function(){
    InventoryReport.draw();
});
$('body').on('click', '.reset-filter', function() {
    $('#filter_part_no').val('');
    $('#filter_part_name').val('');
    $('#filter_category').val('');
    $("#filter_supplier").val('').selectpicker("refresh");
    $("#filter_warehouse").val('').selectpicker("refresh");
    $("#filter_status").val('');
    $("#filter_product_id").val('');
    InventoryReport.draw();
});

// Top 5 Highest Selling Price Product
var Top5HighestSellingPriceProduct = $('#Top5HighestSellingPriceProduct').DataTable({
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
        "url": base_url+"/report/inventory-report/top-5-highest-selling-price-product-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'part_no', name: 'part_no', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'ct', name: 'ct', orderable: false, searchable: false},
        {data: 'pmrprc', name: 'pmrprc', orderable: true, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Top 5 High Profit Product
var Top5HighProfitProduct = $('#Top5HighProfitProduct').DataTable({
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
        "url": base_url+"/report/inventory-report/top-5-high-profit-product-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'part_no', name: 'part_no', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'ct', name: 'ct', orderable: false, searchable: false},
        {data: 'lc_price', name: 'lc_price', orderable: true, searchable: false},
        {data: 'pmrprc', name: 'pmrprc', orderable: true, searchable: false},
        {data: 'total_margin', name: 'total_margin', orderable: true, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Top 5 Highest Moving Invenory Product
var Top5HighestMovingInvenoryProduct = $('#Top5HighestMovingInvenoryProduct').DataTable({
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
        "url": base_url+"/report/inventory-report/top-5-high-moving-inventory-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'pmpno', name: 'pmpno', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'ct', name: 'ct', orderable: false, searchable: false},
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false},
        {data: 'transit_quantity', name: 'transit_quantity', orderable: true, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Top 5 High Damage Product
var Top5HighDamageProduct = $('#Top5HighDamageProduct').DataTable({
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
        "url": base_url+"/report/inventory-report/top-5-high-damage-product-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'pmpno', name: 'pmpno', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'bad_quantity', name: 'bad_quantity', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
// Top 5 High Damage Supplier Product
var Top5HighDamageSupplierProduct = $('#Top5HighDamageSupplierProduct').DataTable({
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
        "url": base_url+"/report/inventory-report/top-5-high-damage-quantity-supplier-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'supplier_name', name: 'supplier_name', orderable: false, searchable: false},
        {data: 'bad_quantity', name: 'bad_quantity', orderable: false, searchable: false},
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'pmpno', name: 'pmpno', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});