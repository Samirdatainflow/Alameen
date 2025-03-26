var StockManagementReportList = $('#StockManagementReportList').DataTable({
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
    "searching": true,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/stock-management-report-list",
        "type": "POST",
        'data': function(data){
            data.filter_part_no=$("#filter_part_no").val();
            data.filter_warehouse=$("#filter_warehouse").val();
            data.filter_from_date=$("#filter_from_date").val();
            data.filter_to_date=$("#filter_to_date").val();
        },
        
    },
    'columns': [
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'pmpno', name: 'pmpno', orderable: false, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'warehouse_name', name: 'warehouse_name', orderable: true, searchable: false},
        {data: 'qty', name: 'qty', orderable: false, searchable: false},
        {data: 'created_date', name: 'created_date', orderable: false, searchable: false}
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});
$("#filter_part_no").on('keyup input',function(){
    StockManagementReportList.draw();
});
$("#filter_warehouse").on('change',function(){
    StockManagementReportList.draw();
});
$('#filter_from_date').datepicker().change(function(){
    StockManagementReportList.draw();
});
$('#filter_to_date').datepicker().change(function(){
    StockManagementReportList.draw();
});
$(".reset-filter").on('click',function(){
    $("#filter_part_no").val('');
    $("#filter_warehouse").val('');
    $('#filter_from_date').val('');
    $('#filter_to_date').val('');
    StockManagementReportList.draw();
});
$('.datepicker').datepicker({
    format  :  'yyyy-mm-dd',
    todayHighlight: true,
    autoclose: true,
}).on('changeDate', function (ev) {
    $(this).datepicker('hide');
});
// Top 5 Stocks in Warehouse
var Top5StocksInWarehouse = $('#Top5StocksInWarehouse').DataTable({
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
    "searching": true,
    "ajax": {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        "url": base_url+"/report/stock-management-report/top-5-stocks-in-warehouse-list",
        "type": "POST",
        'data': function(data){},
        
    },
    'columns': [
        {data: 'warehouse_name', name: 'warehouse_name', orderable: false, searchable: false},
        {data: 'product_id', name: 'product_id', orderable: false, searchable: false},
        {data: 'pmpno', name: 'pmpno', orderable: true, searchable: false},
        {data: 'part_name', name: 'part_name', orderable: false, searchable: false},
        {data: 'product_qtys', name: 'product_qtys', orderable: false, searchable: false},
    ],
    }).on('xhr.dt', function(e, settings, json, xhr) {
});